<!DOCTYPE html>
<html>
    <head>
        <title>Brochure</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
            html, body, div, span, applet, object, iframe, h1, h2, h3, h4, h5, h6, p, blockquote, pre,
            a, abbr, acronym, address, big, cite, code,
            del, dfn, em, img, ins, kbd, q, s, samp,
            small, strike, strong, sub, sup, tt, var,
            b, u, i, center,
            dl, dt, dd, ol, ul, li,
            fieldset, form, label, legend,
            table, caption, tbody, tfoot, thead, tr, th, td,
            article, aside, canvas, details, embed,
            figure, figcaption, footer, header, hgroup,
            menu, nav, output, ruby, section, summary,
            time, mark, audio, video {
                margin: 0;
                padding: 0;
                border: 0;
                font-size: 100%;
                font: inherit;
                vertical-align: baseline;
            }
            /* HTML5 display-role reset for older browsers */
            article, aside, details, figcaption, figure,
            footer, header, hgroup, menu, nav, section {
                display: block;
            }
            body {
                line-height: 1;
            }
            ol, ul {
                list-style: none;
            }
            blockquote, q {
                quotes: none;
            }
            blockquote:before, blockquote:after,
            q:before, q:after {
                content: '';
                content: none;
            }
            table {
                border-collapse: collapse;
                border-spacing: 0;
            }
            @page {
                size:    A4 portrait;
                margin-top: 15mm !important;
            }
            .page-break {
                display: block;
                page-break-before: always;
            }
            body {
                width:   210mm;
                height:  297mm;
            }
            .brandliaison {
                margin-top: 45mm;
            }
            .brandliaison img {
                height: 70px;
            }
            .service-description {
                font-size: 22px !important;
                color: #000;
            }
            .middle-text {
                margin-top: 50mm !important;
                margin-bottom: 10mm !important;
            }
            .site-blue {
                color: #052faa !important;
            }

            .site-orange {
                color: #ed462f !important;
            }
            .site-grey-bg {
                background-color: #e6e6e6;
            }
        </style>
        <!-- UIkit CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/uikit@3.17.11/dist/css/uikit.min.css" />

        <!-- UIkit JS -->
        <script src="https://cdn.jsdelivr.net/npm/uikit@3.17.11/dist/js/uikit.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/uikit@3.17.11/dist/js/uikit-icons.min.js"></script>
    </head>

    <body>
        <div class="uk-section uk-margin-remove uk-padding-large uk-text-center">
            <img class="uk-margin-xlarge-bottom" src="https://ik.imagekit.io/bimma/exportapproval/logo.png?updatedAt=1703831191033">
            <h3 class="uk-text-bold uk-h3 middle-text site-blue">Enabling your products for export to India</h3>
            <h2 class="uk-margin-top uk-margin-remove-bottom uk-h2 uk-text-bold">Obtain {{$service['service_name']}}</h2>
            <div class="service-description uk-margin-small-top">{!!$service_description!!}</div>
            {!!$data['sections']['image']!!}
            <div class="uk-position-bottom brandliaison">
                <span class="uk-text-bold uk-text-small">Powered By</span><br>
                <img src="https://ik.imagekit.io/bimma/exportapproval/bl-logo-hq.jpg?updatedAt=1703831190940">
            </div>
        </div>
        <div class="page-break"></div>
        <div class="uk-section uk-margin-remove uk-padding-large">
            <div class="uk-container">
            {!!$data['sections']['page2']!!}
            </div>
        </div>
        <div class="page-break"></div>
        <div class="uk-section uk-margin-remove uk-padding-large">
            <div class="uk-container">
            {!!$data['sections']['page3']!!}
            </div>
        </div>
        <div class="page-break"></div>
        <div class="uk-section uk-margin-remove uk-padding-large">
            <div class="uk-container">
                <h2 class="uk-h3 uk-text-bold">About Brand Liaison</h2>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">Brand Liaison, founded in 2014, is one of the top 10 compliance consultant companies,
officially registered with the ROC (Registrar of Companies), Ministry of Corporate Affairs,
Government of India. Our commitment to excellence is reflected in our mission to provide
comprehensive assistance and support to deliver exceptional compliance services for
manufacturers.</p>
                <h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Export Assistance</h3>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">With our Export Approval platform, we offer comprehensive assistance and support to
foreign manufacturers aiming to export their products to India seamlessly. Our team of
compliance experts streamlines the process of obtaining essential approvals and
certifications necessary for a diverse range of products. Our expertise lies in facilitating a
smooth and efficient approval process, ensuring that foreign manufacturers meet all
regulatory standards and enabling them to establish a strong presence in the Indian market.</p>
<h3 class="uk-h4 uk-text-bold uk-margin-remove-bottom">Global Partnerships</h3>
                <p class="uk-h4 uk-text-justify uk-margin-remove-top uk-margin-remove-bottom">As a trusted partner, Brand Liaison comprehends the challenges of Indian compliance
services and provides tailored solutions for crucial product certifications necessary for
successful market entry. With an established track record, we have formed enduring
partnerships with over 150 renowned global brands. Our esteemed clientele, such as<br><br>

Aquilstar, C-net, CBS Chemicals, CCDT, Desko, Exza, HID, Hitachi, Jjiuzhou, Willet, QNAP,
Secure-VU, Samsung-STS, Skyworth, and many more, represents our ability to exceed
expectations and meet diverse client needs.</p>
            </div>
        </div>
        <div class="page-break"></div>
        <div class="uk-section uk-margin-remove uk-padding-large">
            <div class="uk-container">
                <h4 class="uk-h6 uk-text-center">This Brochure is created for</h4>
                <p class="uk-h4 uk-text-bold uk-margin-remove uk-text-center">{{$data['name']}}</p>
                <p class="uk-h4 uk-margin-remove uk-text-center">
                {{$data['organisation']}}<br>
                {{$data['email']}}<br>
                {{$data['phone']}}<br>
                {{$data['country']}}
                </p>
                <h4 class="uk-h6 uk-text-bold uk-text-center uk-margin-top"><u>Internal Remarks</u></h4>
                <table class="uk-table uk-table-divider">
                    <tr style="border-bottom: 1px #000 solid;"><td>&nbsp;</td></tr>
                    <tr style="border-bottom: 1px #000 solid;"><td>&nbsp;</td></tr>
                    <tr style="border-bottom: 1px #000 solid;"><td>&nbsp;</td></tr>
                    <tr style="border-bottom: 1px #000 solid;"><td>&nbsp;</td></tr>
                </table>
                <h4 class="uk-h6 uk-text-center" style="margin-top: 40mm;">Connect with us for further assistance</h4>
                <h3 class="uk-h5 uk-text-bolder uk-margin-remove site-blue uk-text-center">Brand Liaison India Pvt. Ltd.</h3>
                <p class="uk-h6 uk-text-bold uk-margin-remove uk-text-center">
                            110, Sharma Complex<br>
                            A-2, Guru Nanak Pura, Laxmi Nagar<br>
                            Delhi - 110092, India
                </p>
                <p class="uk-h6 uk-text-bold uk-text-center">
                            M : +91-9810363988 &nbsp;&nbsp;&nbsp;
                            E: info@bl-india.com<br>
                            www.exportapproval.com
                </p>
                <p class="uk-h5 uk-text-center" style="margin-top: 40mm;">
                    This brochure is created on {{date('d-m-Y')}} at {{date('H:i:s')}} GMT.
                </p>
            </div>
        </div>
    </body>
</html>
