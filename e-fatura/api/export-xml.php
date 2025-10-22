<?php
// e-Fatura XML (UBL-TR) Oluşturma

require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

$db = new Database();
$conn = $db->connect();

$invoiceId = intval($_GET['id'] ?? 0);

if (!$invoiceId) {
    sendError('Fatura ID gerekli');
}

try {
    // Fatura bilgilerini al
    $stmt = $conn->prepare("
        SELECT i.*, c.customer_name, c.customer_type, c.tax_office, c.tax_number,
               c.address, c.district, c.city, c.postal_code, c.phone, c.email,
               comp.company_name, comp.tax_office as comp_tax_office,
               comp.tax_number as comp_tax_number, comp.address as comp_address,
               comp.city as comp_city, comp.phone as comp_phone, comp.email as comp_email
        FROM invoices i
        LEFT JOIN customers c ON i.customer_id = c.id
        LEFT JOIN company comp ON comp.id = 1
        WHERE i.id = ?
    ");
    $stmt->execute([$invoiceId]);
    $invoice = $stmt->fetch();

    if (!$invoice) {
        sendError('Fatura bulunamadı', 404);
    }

    // Fatura kalemlerini al
    $stmt = $conn->prepare("
        SELECT * FROM invoice_items
        WHERE invoice_id = ?
        ORDER BY line_number ASC
    ");
    $stmt->execute([$invoiceId]);
    $items = $stmt->fetchAll();

    // Vergi özeti
    $stmt = $conn->prepare("
        SELECT * FROM invoice_tax_summary
        WHERE invoice_id = ?
        ORDER BY kdv_rate ASC
    ");
    $stmt->execute([$invoiceId]);
    $taxSummary = $stmt->fetchAll();

    // UBL-TR XML Oluştur
    $xml = new DOMDocument('1.0', 'UTF-8');
    $xml->formatOutput = true;

    // Root element
    $root = $xml->createElement('Invoice');
    $root->setAttribute('xmlns', 'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2');
    $root->setAttribute('xmlns:cac', 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2');
    $root->setAttribute('xmlns:cbc', 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2');
    $xml->appendChild($root);

    // UBL Version
    $root->appendChild($xml->createElement('cbc:UBLVersionID', '2.1'));

    // Customization ID
    $root->appendChild($xml->createElement('cbc:CustomizationID', 'TR1.2'));

    // Profile ID
    $root->appendChild($xml->createElement('cbc:ProfileID', $invoice['profile_id']));

    // Fatura ID
    $root->appendChild($xml->createElement('cbc:ID', $invoice['invoice_no']));

    // Copy Indicator
    $root->appendChild($xml->createElement('cbc:CopyIndicator', 'false'));

    // UUID
    $root->appendChild($xml->createElement('cbc:UUID', $invoice['invoice_uuid']));

    // Issue Date
    $root->appendChild($xml->createElement('cbc:IssueDate', date('Y-m-d', strtotime($invoice['invoice_date']))));

    // Issue Time
    $root->appendChild($xml->createElement('cbc:IssueTime', $invoice['invoice_time']));

    // Invoice Type Code
    $typeCode = $invoice['invoice_type'] === 'sales' ? 'SATIS' : 'IADE';
    $root->appendChild($xml->createElement('cbc:InvoiceTypeCode', $typeCode));

    // Document Currency Code
    $root->appendChild($xml->createElement('cbc:DocumentCurrencyCode', $invoice['currency_code']));

    // Line Count
    $root->appendChild($xml->createElement('cbc:LineCountNumeric', count($items)));

    // Şirket Bilgileri (AccountingSupplierParty)
    $supplierParty = $xml->createElement('cac:AccountingSupplierParty');
    $party = $xml->createElement('cac:Party');

    // Şirket Vergi Numarası
    $partyIdentification = $xml->createElement('cac:PartyIdentification');
    $partyIdentification->appendChild($xml->createElement('cbc:ID', $invoice['comp_tax_number']));
    $party->appendChild($partyIdentification);

    // Şirket Adı
    $partyName = $xml->createElement('cac:PartyName');
    $partyName->appendChild($xml->createElement('cbc:Name', $invoice['company_name']));
    $party->appendChild($partyName);

    // Şirket Adresi
    $postalAddress = $xml->createElement('cac:PostalAddress');
    $postalAddress->appendChild($xml->createElement('cbc:StreetName', $invoice['comp_address']));
    $postalAddress->appendChild($xml->createElement('cbc:CityName', $invoice['comp_city']));
    $country = $xml->createElement('cac:Country');
    $country->appendChild($xml->createElement('cbc:Name', 'Türkiye'));
    $postalAddress->appendChild($country);
    $party->appendChild($postalAddress);

    // Vergi Dairesi
    $partyTaxScheme = $xml->createElement('cac:PartyTaxScheme');
    $taxScheme = $xml->createElement('cac:TaxScheme');
    $taxScheme->appendChild($xml->createElement('cbc:Name', $invoice['comp_tax_office']));
    $partyTaxScheme->appendChild($taxScheme);
    $party->appendChild($partyTaxScheme);

    $supplierParty->appendChild($party);
    $root->appendChild($supplierParty);

    // Müşteri Bilgileri (AccountingCustomerParty)
    $customerParty = $xml->createElement('cac:AccountingCustomerParty');
    $party = $xml->createElement('cac:Party');

    // Müşteri Vergi/TC Numarası
    $partyIdentification = $xml->createElement('cac:PartyIdentification');
    $partyIdentification->appendChild($xml->createElement('cbc:ID', $invoice['tax_number'] ?: 'N/A'));
    $party->appendChild($partyIdentification);

    // Müşteri Adı
    $partyName = $xml->createElement('cac:PartyName');
    $partyName->appendChild($xml->createElement('cbc:Name', $invoice['customer_name']));
    $party->appendChild($partyName);

    // Müşteri Adresi
    $postalAddress = $xml->createElement('cac:PostalAddress');
    $postalAddress->appendChild($xml->createElement('cbc:StreetName', $invoice['address']));
    $postalAddress->appendChild($xml->createElement('cbc:CityName', $invoice['city']));
    $country = $xml->createElement('cac:Country');
    $country->appendChild($xml->createElement('cbc:Name', 'Türkiye'));
    $postalAddress->appendChild($country);
    $party->appendChild($postalAddress);

    // Vergi Dairesi
    if ($invoice['tax_office']) {
        $partyTaxScheme = $xml->createElement('cac:PartyTaxScheme');
        $taxScheme = $xml->createElement('cac:TaxScheme');
        $taxScheme->appendChild($xml->createElement('cbc:Name', $invoice['tax_office']));
        $partyTaxScheme->appendChild($taxScheme);
        $party->appendChild($partyTaxScheme);
    }

    $customerParty->appendChild($party);
    $root->appendChild($customerParty);

    // KDV Toplamı (TaxTotal)
    $taxTotal = $xml->createElement('cac:TaxTotal');
    $taxTotal->appendChild($xml->createElement('cbc:TaxAmount', number_format($invoice['total_kdv'], 2, '.', '')))
             ->setAttribute('currencyID', $invoice['currency_code']);

    // Her KDV oranı için TaxSubtotal
    foreach ($taxSummary as $tax) {
        $taxSubtotal = $xml->createElement('cac:TaxSubtotal');
        $taxSubtotal->appendChild($xml->createElement('cbc:TaxableAmount', number_format($tax['taxable_amount'], 2, '.', '')))
                    ->setAttribute('currencyID', $invoice['currency_code']);
        $taxSubtotal->appendChild($xml->createElement('cbc:TaxAmount', number_format($tax['tax_amount'], 2, '.', '')))
                    ->setAttribute('currencyID', $invoice['currency_code']);

        $taxCategory = $xml->createElement('cac:TaxCategory');
        $taxCategory->appendChild($xml->createElement('cbc:Percent', number_format($tax['kdv_rate'], 2, '.', '')));
        $taxScheme = $xml->createElement('cac:TaxScheme');
        $taxScheme->appendChild($xml->createElement('cbc:Name', 'KDV'));
        $taxScheme->appendChild($xml->createElement('cbc:TaxTypeCode', '0015'));
        $taxCategory->appendChild($taxScheme);
        $taxSubtotal->appendChild($taxCategory);

        $taxTotal->appendChild($taxSubtotal);
    }

    $root->appendChild($taxTotal);

    // Fatura Toplamı (LegalMonetaryTotal)
    $monetaryTotal = $xml->createElement('cac:LegalMonetaryTotal');
    $monetaryTotal->appendChild($xml->createElement('cbc:LineExtensionAmount', number_format($invoice['subtotal'], 2, '.', '')))
                  ->setAttribute('currencyID', $invoice['currency_code']);
    $monetaryTotal->appendChild($xml->createElement('cbc:TaxExclusiveAmount', number_format($invoice['subtotal'], 2, '.', '')))
                  ->setAttribute('currencyID', $invoice['currency_code']);
    $monetaryTotal->appendChild($xml->createElement('cbc:TaxInclusiveAmount', number_format($invoice['total_amount'], 2, '.', '')))
                  ->setAttribute('currencyID', $invoice['currency_code']);
    $monetaryTotal->appendChild($xml->createElement('cbc:PayableAmount', number_format($invoice['total_amount'], 2, '.', '')))
                  ->setAttribute('currencyID', $invoice['currency_code']);
    $root->appendChild($monetaryTotal);

    // Fatura Kalemleri (InvoiceLine)
    foreach ($items as $item) {
        $invoiceLine = $xml->createElement('cac:InvoiceLine');

        // Satır numarası
        $invoiceLine->appendChild($xml->createElement('cbc:ID', $item['line_number']));

        // Miktar
        $invoiceLine->appendChild($xml->createElement('cbc:InvoicedQuantity', number_format($item['quantity'], 3, '.', '')))
                    ->setAttribute('unitCode', $item['unit']);

        // Satır toplamı
        $invoiceLine->appendChild($xml->createElement('cbc:LineExtensionAmount', number_format($item['line_total'], 2, '.', '')))
                    ->setAttribute('currencyID', $invoice['currency_code']);

        // Ürün bilgisi
        $itemElement = $xml->createElement('cac:Item');
        $itemElement->appendChild($xml->createElement('cbc:Name', $item['product_name']));
        if ($item['description']) {
            $itemElement->appendChild($xml->createElement('cbc:Description', $item['description']));
        }
        $invoiceLine->appendChild($itemElement);

        // Fiyat
        $price = $xml->createElement('cac:Price');
        $price->appendChild($xml->createElement('cbc:PriceAmount', number_format($item['unit_price'], 2, '.', '')))
              ->setAttribute('currencyID', $invoice['currency_code']);
        $invoiceLine->appendChild($price);

        $root->appendChild($invoiceLine);
    }

    // XML'i dosyaya kaydet
    $filename = 'efatura_' . $invoice['invoice_no'] . '.xml';
    $filepath = XML_PATH . $filename;

    $xml->save($filepath);

    // Dosya yolunu veritabanına kaydet
    $stmt = $conn->prepare("UPDATE invoices SET xml_path = ? WHERE id = ?");
    $stmt->execute([$filename, $invoiceId]);

    // XML'i indir
    header('Content-Type: application/xml; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . filesize($filepath));
    readfile($filepath);
    exit;

} catch (Exception $e) {
    sendError('XML oluşturulurken hata: ' . $e->getMessage(), 500);
}
