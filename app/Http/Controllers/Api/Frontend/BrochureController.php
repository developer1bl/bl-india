<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Countries;
use App\Models\Service;
use App\Models\Leads;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use  Illuminate\Support\Facades\Validator;

class BrochureController extends Controller
{
    /**
     * this function is handle the submitted BrochureForm.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function submitBrochureForm(Request $request){

        $validator = Validator::make($request->all(), [
            'fullname' => 'required|string|max:255',
            'organisation' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'mobile' => 'required|string|max:15', // Assuming mobile is a string
            'country' => 'required',
            'service' => 'required|numeric|exists:services,service_id', // Assuming service is a numeric ID referencing a service
            'source' => 'required|string|max:255',
            'message' => 'nullable|string',
        ]);

        //if the request have some validation errors
        if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        $countries = $request->country ?? null;

        if ($countries) {
           // Removing brackets and spaces from the string
            $requestValue = str_replace(['[', ']', ' '], '', $countries);
            // Exploding the string into an array
            $requestArray = explode(',', $requestValue);
            // Extracting values
            $country = $requestArray[0];
            $phonecode = $requestArray[1];
        }else{
            $country = null;
            $phonecode = null;
        }

        $data = [
            'name' => $request->fullname,
            'organisation' => $request->organisation,
            'email' => $request->email,
            'country' => $country,
            'phone' => '+'.$phonecode.'-'.$request->mobile,
            'service' => $request->service,
            'source' => $request->source,
            'message' => $request->message,
            'status' => 'open',
            'ip_address' => $request->ip(),
        ];

        //due to some all reason we are preventing leads from here
        if($country != 'ZIMBABWE') {

            //storing the lead
            $lead = Leads::create($data);
            $id = $lead->id;
        }

        $service = Service::find($request->service)
                            ->select('services.service_name', 'services.service_description')
                            ->first();

        $data['service'] = $service;

        $section = [
            3 =>[
                'image' => '<img src="https://ik.imagekit.io/iouishbjd/exportapproval/bis-crs.webp?updatedAt=1707116094040" height="150" width="150"/>',
                'page2' => '<h2 class="uk-h3 uk-text-bold">BIS/CRS Registration: Mandatory Before Export to India</h2>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">The Bureau of Indian Standards (BIS) is the national standards body of India, responsible for developing and maintaining quality standards for various products before export to India. BIS Registration ensures that products meet the specified quality and safety criteria, promoting consumer protection and industry excellence. Within the framework of BIS, there is a specific scheme called the Compulsory Registration Scheme (CRS) for certain IT and electronic products. CRS is designed to regulate the quality of these products, and compliance with CRS is mandatory for manufacturers and exporters. If you are looking to export IT and electronic products to India, obtaining BIS/CRS Registration from BIS is a mandatory requirement. Without this necessary registration, you will not be able to export your products to India, emphasizing the importance of compliance with BIS standards for seamless international trade.</p>
                <h2 class="uk-h3 uk-text-bold">Products Requiring BIS/CRS Registration for Export to India</h2>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">BIS/CRS Registration is mandatory for various IT and electronic products while exporting to India. This registration ensures that the products meet the prescribed quality and safety standards set by the Bureau of Indian Standards. It is important for exporters to identify the specific product categories covered by BIS/CRS and ensure compliance before exporting to India. Click here to view the mandatory product list of BIS/CRS Registration.</p>

                ',
                'page3' => '<h2 class="uk-h3 uk-text-bold">BIS/CRS Registration Procedure</h2>

                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Step 1: Product Categorization</h3>

                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Step 2: Testing and Analysis</h3>

                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Step 3: Documentation Preparation</h3>

                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Step 4: Application Submission</h3>

                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Step 5: Factory Inspection</h3>

                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Step 6: Certification Issuance</h3>

                <h2 class="uk-h3 uk-text-bold">Required Documents for BIS/CRS Registration</h2>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">The BIS/CRS registration process requires the submission of specific documents, including:</p>
                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Product Information</h3>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">Detailed information about the IT or electronic product, including specifications, features, and usage.</p>
                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Test Reports</h3>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">Comprehensive reports from accredited laboratories detailing the results of tests conducted to assess the product`s conformity with relevant Indian standards.</p>
                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Technical Documentation</h3>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">Complete technical documentation outlining the design, construction, and operation of the product.</p>
                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Manufacturing Process Details</h3>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">Information about the manufacturing process, quality control measures, and adherence to standards at the production facility.</p>
                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Declaration of Conformity</h3>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">A formal declaration stating that the product conforms to the applicable BIS standards.</p>
                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Application Form</h3>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">The completed BIS/CRS application form with accurate and detailed information about the product.</p>
                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Authorized Indian Representative (AIR) Letter</h3>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">A letter designating an authorized representative in India.</p>
                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Trademark/Brand Authorization</h3>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">Authorization for the use of trademarks or brand names associated with the product.</p>
                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Product Sample</h3>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">In some cases, a sample of the product may be required for further evaluation.</p>
                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Proof of Payment</h3>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">Receipt or proof of payment for the applicable registration fees.<strong> </strong></p>
                <h2 class="uk-h3 uk-text-bold">BIS/CRS Registration Timeline</h2>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom"><strong>For the primary lead model:</strong> 20-30 working days</p>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom"><strong>For subsequent lead models (inclusion): </strong>15-20 working days<strong> </strong></p>
                <h2 class="uk-h3 uk-text-bold">BIS/CRS Registration Costing</h2>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top">
                Costing inclusive of government fees for BIS/CRS Registration is <strong>starting from $1199.00.</strong></p>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom"><strong>Note:</strong> This cost may vary depending on the specific product category and the quantity of units that need to be marked with the IS label.</p>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">For customized quotations of BIS/CRS Registration costing, feel free contact us at +91-9250056788 or +91-8130856678.</p>'
            ],
            4 => [
                'image' => '<img src="https://ik.imagekit.io/iouishbjd/exportapproval/bis-isi.webp?updatedAt=1707116093909" height="150" width="150"/>',
                'page2' => '<h2 class="uk-h3 uk-text-bold">BIS/ISI Certification: Mandatory Before Export to India</h2>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">
                The Bureau of Indian Standards (BIS) plays a pivotal role in maintaining the quality and
                safety standards of various products through its stringent regulatory framework. The Indian
                Standards Institute (ISI) certification, identified by the ISI mark, is a symbol of compliance
                with BIS specifications. BIS/ISI certification is applicable to a wide range of products,
                including electronics, automotive components, cables, and more. If you are looking to
                export such items to India, it is essential to obtain the BIS/ISI certification as mandated by
                the Bureau of Indian Standards. This certification ensures that the products adhere to the
                prescribed quality standards, assuring consumers of their safety and reliability. Without the
                ISI mark, exporting these specific products to India is not permitted, making it a legal
                requirement for market entry. Exporters must prioritize obtaining BIS/ISI certification for
                their products to align with BIS regulations, facilitating successful entry into the Indian
                market.<p>
                <h2 class="uk-h3 uk-text-bold">Products Requiring BIS/ISI Certification for Export to India</h2>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">
                There are certain products that require BIS/ISI Certification to export to India. This
                certification is mandated to ensure that these products meet specific quality and safety
                standards set by the Bureau of Indian Standards. Exporters must obtain the necessary
                BIS/ISI certification for their products to gain access to the Indian market and adhere to the
                regulatory requirements.<p>',
                'page3' => '<h2 class="uk-h3 uk-text-bold">BIS/ISI Certification Procedure</h2>

                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">
                Step 1: Product Identification</h3>

                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">
                Step 2: Preliminary Testing</h3>

                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">
                Step 3: Application Submission</h3>

                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">
                Step 4: Factory Inspection</h3>

                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">
                Step 5: Sample Testing</h3>

                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">
                Step 6: Certification Issuance</h3>

                <h2 class="uk-h3 uk-text-bold">Required Documents for BIS/ISI Certification</h2>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">
                The BIS/ISI Certification process requires the submission of specific documents, including:
                </p>
                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Product Information</h3>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">
                Detailed information about the product, encompassing specifications, features, and
                intended use.</p>
                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">
                Test Reports</h3>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">
                Comprehensive reports from accredited laboratories detailing the results of tests conducted
                to assess the product&#39;s conformity with relevant Indian standards.</p>
                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">
                Technical Documentation</h3>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">
                Complete technical documentation outlining the design, construction, and operation of the
                product.</p>
                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">
                Manufacturing Process Details</h3>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">
                Information about the manufacturing process, quality control measures, and adherence to
                standards at the production facility.</p>
                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">
                Declaration of Conformity</h3>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">
                A formal declaration affirming that the product conforms to the applicable BIS standards.</p>
                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">
                Factory Inspection Report</h3>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">
                If applicable, a report detailing the findings of the factory inspection conducted by BIS
                authorities.</p>
                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">
                Application Form</h3>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">
                The completed BIS/ISI application form with accurate and detailed information about the
                product.</p>
                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">
                Trademark/Brand Authorization</h3>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">
                Authorization for the use of trademarks or brand names associated with the product.</p>
                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">
                Proof of Payment</h3>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">
                Receipt or proof of payment for the applicable certification fees.</p>
                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">
                Authorized Indian Representative (AIR) Authorization Letter</h3>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">
                An authorization letter designating an Authorized Indian Representative.</p>

                <h2 class="uk-h3 uk-text-bold">BIS/ISI Certification Timeline</h2>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">
                Approximately: 5-6 months</p>

                <h2 class="uk-h3 uk-text-bold">BIS/ISI Certification Costing<h2>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">
                        Costing inclusive of government fees for BIS/ISI Certification is <strong>starting from $8999.00.</strong></p>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">
                <strong>Note:</strong> This cost may vary depending on the specific product category and the quantity of
                units that need to be marked with the ISI label.</p>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">
                For customized quotations of BIS/ISI Certification costing, feel free contact us at +91-
                9250056788 or +91-8130856678.</p>',
            ],
            5 => [
                'image' => '<img src="https://ik.imagekit.io/iouishbjd/exportapproval/wpc-eta.webp?updatedAt=1707116094316" height="150" width="150"/>',
                'page2' => '<h2 class="uk-h3 uk-text-bold">WPC ETA Approval: Mandatory Before Export to India</h2>
                 <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">The Wireless Planning and Coordination (WPC) in India is a regulatory authority that oversees the efficient use of the radio frequency spectrum. Within the framework of WPC, Equipment Type Approval (ETA) is a crucial aspect for Wi-Fi and Bluetooth-enabled products. As a subset of WPC regulations, ETA Approval ensures that these products meet the necessary technical standards and do not interfere with other communication systems. For exporters looking to market their Wi-Fi and Bluetooth-enabled devices to India, obtaining the ETA certificate from WPC is mandatory. WPC ETA Approval signifies compliance with the specified standards, and without it, exporters would encounter restrictions, and their products would not be eligible for export to India. Exporters must undergo the ETA Certification process to align their products with WPC regulations, ensuring a smooth entry into the Indian market and demonstrating adherence to the established standards for wireless communication devices.</p>
                <h2 class="uk-h3 uk-text-bold">Products Requiring WPC ETA Approval for Export to India<strong><br></strong></h2>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">Various electronic products with Wi-Fi and Bluetooth capabilities fall under the category of items that require WPC ETA Approval for export to India. WPC ETA Approval is essential for such products to ensure compliance with specific technical standards and regulatory requirements set by the Wireless Planning and Coordination Wing. Manufacturers of Wi-Fi and Bluetooth-enabled devices must prioritise obtaining WPC ETA approval to facilitate the entry of their products into the Indian market.</p>',
                'page3' => '<h2 class="uk-h3 uk-text-bold">WPC ETA Approval Procedure<strong><br></strong></h2>

                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Step 1: Product Categorization</h3>

                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Step 2: Documentation Preparation</h3>

                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Step 3: Application Submission</h3>

                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Step 4: Frequency Allocation</h3>

                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Step 5: Testing</h3>

                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Step 6: Factory Inspection</h3>

                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Step 7: Certification Issuance</h3>

                <h2 class="uk-h3 uk-text-bold">Required Documents for WPC ETA Approval<strong><br></strong></h2>
                 <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">The WPC ETA Approval process requires the submission of specific documents, including:</p>
                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Product Information</h3>
                 <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">Detailed information about the wireless or electronic product, including specifications, features, and intended use.</p>
                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Technical Documentation</h3>
                 <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">Comprehensive technical documentation outlining the design, circuit diagrams, and operation of the product.</p>
                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">User Manual</h3>
                 <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">A user manual providing detailed instructions for the operation and usage of the product.</p>
                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Application Form</h3>
                 <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">The completed WPC ETA application form with accurate and detailed information about the product.</p>

                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Authorization Letter for AIR</h3>
                 <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">An authorization letter designating an Authorized Indian Representative (AIR) required by WPC for the specific product category.</p>

                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Test Reports</h3>
                 <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">EMC (Electromagnetic Compatibility) and RF (Radio Frequency) test reports from accredited laboratories demonstrating the product`s compliance with regulatory standards.</p>

                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Frequency Allocation Details</h3>
                 <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">Specify the frequency bands and wireless parameters associated with the product. Obtain the necessary frequency allocation from WPC, if applicable.</p>
                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Proof of Payment</h3>
                 <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">Receipt or proof of payment for the applicable WPC ETA certification fees.</p>
                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Declaration of Conformity</h3>
                 <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">A formal declaration affirming that the product conforms to the relevant WPC standards.</p>
                <h2 class="uk-h3 uk-text-bold">WPC ETA Approval Timeline</h2>
                 <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom"><strong>Standard Timeline</strong>: Within 10 working days<strong><br></strong></p>
                <h2 class="uk-h3 uk-text-bold">WPC ETA Approval Costing</h2>
                 <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">
                 Costing inclusive of government fees for WPC Certification is <strong>starting from $499.00.</strong>
                 </p>
                 <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom"><strong>Note</strong>: This cost may vary depending on the specific product category and the quantity of units that require the WPC certificate.</p>
                 <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">For customized quotations of WPC ETA Approval costing, feel free to contact us at +91-9250056788 or +91-8130856678.</p>',
            ],
            6 => [
                'image' => '<img src="https://ik.imagekit.io/iouishbjd/exportapproval/tec.webp?updatedAt=1707116094401" height="150" width="150"/>',
                'page2' => '<h2 class="uk-h3 uk-text-bold">TEC Certification: Mandatory Before Export to India</h2>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">Telecommunication Engineering Centre (TEC) Certification is a crucial requirement for various telecom products destined for the Indian market. TEC, under the Department of Telecommunications, India, is responsible for formulating and implementing standards for telecommunications equipment. The TEC Certification ensures that telecom products comply with the specified technical and quality standards, addressing aspects such as safety, reliability, and performance. For manufacturers seeking to export their telecom products to India, obtaining TEC Certification is a legal requirement. Without this certification, exporting specific telecom products to India is not permitted. Manufacturers must undergo the TEC Certification process to validate that their products align with the established standards set by TEC, ensuring seamless entry into the Indian market.</p>
                <h2 class="uk-h3 uk-text-bold">Products Requiring TEC Certification for Export to India</h2>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">There are certain electronic products that need TEC certification for export to India. This certification ensures that the products comply with technical standards and regulations set by TEC, guaranteeing their safety, performance, and interoperability within the Indian telecommunications network. Manufacturers of these electronic products must obtain TEC certification to meet the mandatory requirements for entry into the Indian market.</p>',
                'page3' => '<h2 class="uk-h3 uk-text-bold">TEC Certification Procedure</h2>

                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Step 1: Registration on the TEC Portal</h3>

                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Step 2: Allotment of Testing Laboratory</h3>

                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Step 3: Submission of Samples for Testing</h3>

                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Step 4: Receipt of Test Reports</h3>

                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Step 5: Document Submission and Payment to TEC</h3>

                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Step 6: Certificate Granting After Successful Verification</h3>

                <h2 class="uk-h3 uk-text-bold">Required Documents for TEC Certification</h2>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">The TEC Certification process requires the submission of specific documents, including:</p>
                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Registration Details</h3>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">Provide registration details and complete the registration process on the TEC portal.</p>
                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Authorization Letter for AIR</h3>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">Submit an Authorization Letter designating an Authorized Indian Representative (AIR) required by TEC for the specific product category.</p>
                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Bill of Material (BoM)</h3>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">Include a detailed Bill of Material (BoM) specifying all components and materials used in the telecommunication equipment.</p>
                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Technical Documentation</h3>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">Compile comprehensive technical documentation, including circuit diagrams, specifications, and user manuals.</p>
                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Test Reports</h3>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">Provide test reports from accredited laboratories, demonstrating the product`s conformity with TEC standards.</p>
                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Product Sample</h3>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">Submit samples of the telecommunication equipment for testing and evaluation by the designated laboratory.</p>
                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Frequency Allocation Details</h3>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">Specify the frequency bands and other wireless parameters associated with the product. Obtain the necessary frequency allocation from TEC, if applicable.</p>
                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Application Form</h3>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">Complete the TEC Certification application form with accurate and detailed information about the product.</p>
                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Proof of Payment</h3>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">Include the receipt or proof of payment for the applicable TEC Certification fees.</p>
                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Declaration of Conformity</h3>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">Provide a formal declaration affirming that the product conforms to the relevant TEC standards.</p>
                <h2 class="uk-h3 uk-text-bold">TEC Certification Timeline</h2>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">Standard Timeline: 30 to 60 working days</p>
                <h2 class="uk-h3 uk-text-bold">TEC Certification Costing</h2>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">        Costing inclusive of government fees for TEC Certification is <strong>starting from $9999.00.</strong></p>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom"><strong>Note</strong>: This cost may vary depending on the specific product category and the quantity of units that require the TEC certificate.</p>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">For customized quotations of TEC Certification costing, feel free to contact us at +91-9250056788 or +91-8130856678.</p>',
                ]
            ];

        //based on selected service section is given
        $data['sections'] = $section[$request->service];

        $pdf = PDF::loadView('pdf.brochureDownload', compact(['service', 'data']));

        if($country != 'ZIMBABWE') {

            // Subject
            $subject = "Thanks for downloading brochure";

            // Message
            $thanks = "<p style='font-family: Arial, Helvetica, sans-serif; font-size: 18px; color: #000;'>Hello ".$data['name'].",<br/>".
            "Thank you for downloading our brochure for <b>".$service['service_name']."</b>!</p>".
            "<p style='font-family: Arial, Helvetica, sans-serif; font-size: 18px; color: #000;'>We appreciate your interest in Export Approval, powered by Brand Liaison - a compliance consultant company offering comprehensive support to foreign manufacturers in obtaining required Indian approvals and certifications to export their products to India. Our Export Approval platform is designed to provide seamless assistance, ensuring that your products meet the required standards for successful international trade.</p>".
            "<p style='font-family: Arial, Helvetica, sans-serif; font-size: 18px; color: #000;'>Our team has received your interest, and we want to assure you that we are here to assist you promptly. You can expect to hear from us within the next 6 working hours.</p>".
            "<p style='font-family: Arial, Helvetica, sans-serif; font-size: 18px; color: #000;'>If you have any immediate questions or concerns, feel free to reach out to us at +91-9810363988.</p>".
            "<p style='font-family: Arial, Helvetica, sans-serif; font-size: 18px; color: #000;'>Wishing you a great day ahead!</p>".
            "<p style='font-family: Arial, Helvetica, sans-serif; font-size: 18px; color: #000;'>Best regards,<br>".
            "Team Brand Liaison<br>".
            "Contact No: +91-9250056788, +91-8130615678<br>".
            "Email: info@bl-india.com</p>";

            // To send HTML mail, the Content-type header must be set
            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";

            // Additional headers
            $headers .= 'From: Team Export Approval <no-reply@exportapproval.com>' . "\r\n";

            mail($data['email'], $subject, $thanks, $headers);
        }

        //if directory is not exist then create a new directory for store pdf files
        $pdfDirectory = storage_path('app/public/PDF');
        if (!File::exists($pdfDirectory)) {
            File::makeDirectory($pdfDirectory, 0755, true);
        }

        // Generate a unique filename
        $filename = 'Brochure-'.$service['service_name'].'-'.time().'.pdf';

        // Store the PDF in the storage path
        $pdf->save($pdfDirectory . '/' . $filename);

        return $filename;
    }

    /**
     * Function to delete a PDF file by its name.
     *
     * @param string $filename
     * @return bool
     */
    public function deleteBrochurePdf($filename)
    {
        $directory = 'public/PDF';
        $filePath = $directory . '/' . $filename;

        if (Storage::exists($filePath)) {
            // File exists, $filePath contains the path to the file
            return Storage::delete($filePath);
        } else {
            // File does not exist
            return false;
        }
    }
}
