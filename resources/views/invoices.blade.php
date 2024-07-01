<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Qotoon Inovice | فاتورة قطون</title>

    <style>
        @media print {
            @page {
                /* size: A4; */
                margin: 20mm;
            }

            body {
                margin: 0;
            }

            .container {
                padding: 0;
            }
        }

        .rtl {
            direction: rtl;
        }

        .ltr {
            direction: ltr;
        }

        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }
    </style>
</head>
@php
$invoice =  \App\Models\Invoice::find(1);
 @endphp
<body dir="rtl">

    {{-- <div style=" font-size: 8px;"> --}}
        <table style="width: 100%;">

            <tr>
                <td style="width: 40%; overflow-x: hidden;">
                    <p style="margin-bottom: 8px">شركة قطون للسفر والسياحة</p>
                    <table style="width: 100%; gap: 12px;">
                        <tr>
                            <td style="width: fit-content; max-width: 50%;">
                                <ul style="list-style: none;">
                                    <li style="white-space: nowrap;">هاتف</li>
                                    <li style="white-space: nowrap;">فاكس</li>
                                    <li style="white-space: nowrap;">الرقم الضريبي</li>
                                    <li style="white-space: nowrap;">العنوان</li>
                                    <li style="white-space: nowrap;">جوال</li>
                                    <li style="white-space: nowrap;">البريد الالكتروني</li>
                                    <li style="white-space: nowrap;">رقم السجل التجاري</li>
                                </ul>
                            </td>
                            <td style="width: fit-content; max-width: 50%;">
                                <ul style="list-style: none;">
                                    <li style="white-space: nowrap;">{{  $invoice->mobile}}</li>
                                    <li style="white-space: nowrap;">{{  $invoice->fax}}</li>
                                    <li style="white-space: nowrap;">{{  $invoice->tax_number}}</li>
                                    <li style="white-space: nowrap;">{{  $invoice->ar_address}}</li>
                                    <li style="white-space: nowrap;">{{  $invoice->phone}}</li>
                                    <li style="white-space: nowrap;">{{  $invoice->email}}</li>
                                    <li style="white-space: nowrap;">{{  $invoice->CRN}}</li>
                                </ul>
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width: 20%;">
                    <div style="max-width: 90px">
                        <img class="width: 100%; height: 100%" src="{{asset('uploads/invoice/' . $invoice->img) }}" alt="">
                    </div>
                </td>
                <td style="width: 40%; overflow-x: hidden;" dir="ltr">
                    <p style="margin-bottom: 8px">Co .T .Q</p>
                    <table style="width: 100%; gap: 12px;">
                        <tr>
                            <td style="width: fit-content; max-width: 50%;">
                                <ul style="list-style: none;">
                                    <li style="white-space: nowrap;">Phone</li>
                                    <li style="white-space: nowrap;">Fax</li>
                                    <li style="white-space: nowrap;">Tax Number</li>
                                    <li style="white-space: nowrap;">Address</li>
                                    <li style="white-space: nowrap;">C. Phone</li>
                                    <li style="white-space: nowrap;">E-Mail</li>
                                    <li style="white-space: nowrap;">Commercial Reg.</li>
                                </ul>
                            </td>
                            <td style="width: fit-content; max-width: 50%;">
                                <ul style="list-style: none;">
                                    <li style="white-space: nowrap;">{{  $invoice->mobile}}</li>
                                    <li style="white-space: nowrap;">{{  $invoice->fax}}</li>
                                    <li style="white-space: nowrap;">{{  $invoice->tax_number}}</li>
                                    <li style="white-space: nowrap;">{{  $invoice->address}}</li>
                                    <li style="white-space: nowrap;">{{  $invoice->phone}}</li>
                                    <li style="white-space: nowrap;">{{  $invoice->email}}</li>
                                    <li style="white-space: nowrap;">{{  $invoice->CRN}}</li>

                                </ul>
                            </td>
                    </table>
                </td>
            </tr>
        </table>


        <hr style="margin: 4px 0;" />
        <table style="width: 100%; gap: 8px;">
            <tr>
                <td style="width: 50%;">
                    <div style="width: 80px; height: 80px; margin: 0 auto;">
                        {!! QrCode::generate(route('show-invoice', [$booked->order_payment->invoice_number])) !!}
                    </div>


                </td>
                <td style="width: 50%;">
                    <div style="background-color: #e5e7eb; width: fit-content; padding: 4px; margin-bottom: 2px;">
                        <p>فاتورة مبيعات ضريبة مبسطة</p>
                    </div>
                    <p>Simplified tax invoice</p>
                    <br>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tbody>
                            <tr>
                                <td style="border: 1px solid black; padding: 4px;">تاريخ اصدار الفاتورة:
                                    <pre style="display: inline"> {{  $booked->order_payment->updated_at->format('d-m-Y')}}  </pre>
                                </td>
                                <td dir="ltr" style="border: 1px solid black; padding: 4px;">Invoice Issuance Date:
                                    <pre style="display: inline">{{  $booked->order_payment->updated_at->format('d-m-Y')}} </pre>
                                </td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black; padding: 4px;">رقم  الفاتورة : {{  $booked->order_payment->invoice_number}}</td>
                                <td dir="ltr" style="border: 1px solid black; padding: 4px;">Invoice Number: {{  $booked->order_payment->invoice_number}}</td>
                            </tr>
                        </tbody>
                    </table>

                </td>
            </tr>
        </table>
        <br />
        <table style="width: 100%; gap: 8px;">
            <tr>
                <td style="width: 50%;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr>
                                <th colspan="2" style="font-size: 5px; border: 1px solid black; padding: 4px 2px">بيانات
                                    العميل</th>
                                <th style="border: 1px solid black; padding: 4px 2px">Customer Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="border: 1px solid black; padding: 2px;">اسم العميل</td>
                                <td style="border: 1px solid black; padding: 2px;     width: 20%;">{{  $booked->user->name }}</td>
                                <td style="border: 1px solid black; padding: 2px;" dir="ltr">Customer Name</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black; padding: 2px;">طريقة الدفع</td>
                                <td style="border: 1px solid black; padding: 2px;">Tabby /Fatoorah </td>
                                <td style="border: 1px solid black; padding: 2px;" dir="ltr">Payment Method</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black; padding: 2px;">تاريخ الدفع</td>
                                <td style="border: 1px solid black; padding: 2px;">
                                    <pre>  {{  $booked->order_payment->created_at }}</pre>
                                </td>
                                <td style="border: 1px solid black; padding: 2px;" dir="ltr">Payment Date</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black; padding: 2px;">تسجيل الوصول</td>
                                <td style="border: 1px solid black; padding: 2px;">
                                    <pre> {{   Carbon\Carbon::parse($booked->date_from)  }}</pre>
                                </td>
                                <td style="border: 1px solid black; padding: 2px;" dir="ltr">Check-in</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black; padding: 2px;">تسجيل المغادرة</td>
                                <td style="border: 1px solid black; padding: 2px;">
                                    <pre>  {{  Carbon\Carbon::parse($booked->date_to)  }}</pre>
                                </td>
                                <td style="border: 1px solid black; padding: 2px;" dir="ltr">Check-out</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black; padding: 2px;">عدد الليالي المدفوعة</td>
                                <td style="border: 1px solid black; padding: 2px;     width: 20%;"></td>
                                <td style="border: 1px solid black; padding: 2px;" dir="ltr">{{   Carbon\Carbon::parse($booked->date_from)->diffInDays(Carbon\Carbon::parse($booked->date_to)) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td style="width: 50%;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr>
                                <th colspan="2" style="border: 1px solid black; padding: 4px 2px;">بيانات الشقة</th>
                                <th style="border: 1px solid black; padding: 4px 2px; width: 45%;">Apartment Data</th>
                            </tr>
                        </thead>

                        <tbody>

                            <tr>
                                <td style="border: 1px solid black; padding: 2px;">اسم الحي</td>
                                <td style="border: 1px solid black; padding: 2px;     width: 20%;">{{  $booked->apartment->area->name}}</td>
                                <td style="border: 1px solid black; padding: 2px;" dir="ltr">District</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black; padding: 2px;">رقم العمارة</td>
                                <td style="border: 1px solid black; padding: 2px;     width: 20%;"></td>
                                <td style="border: 1px solid black; padding: 2px;" dir="ltr">Building No</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black; padding: 2px;">رقم الشقة</td>
                                <td style="border: 1px solid black; padding: 2px;     width: 20%;">{{  $booked->apartment->code}}</td>
                                <td style="border: 1px solid black; padding: 2px;" dir="ltr">Apartment No</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black; padding: 2px;">رقم الحجز</td>
                                <td style="border: 1px solid black; padding: 2px;     width: 20%;">{{  $booked->id}}</td>
                                <td style="border: 1px solid black; padding: 2px;" dir="ltr">Reservation No</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black; padding: 2px;">متوفر خدمة الإنترنت</td>
                                <td style="border: 1px solid black; padding: 2px;     width: 20%;">{{  !empty($booked->apartment->internet_name)?'متوفر':'غير متوفر'}}</td>
                                <td style="border: 1px solid black; padding: 2px;" dir="ltr">Internet Service is
                                    Available
                                </td>
                            </tr>
                        </tbody>
                    </table>

                </td>
            </tr>
        </table>
        <br />
        <br />
        <div style="width: 100%;">
            <p style="text-decoration: underline; margin: 0 auto; width: fit-content;">تفاصيل الفاتورة - details Invoice
            </p>
        </div>
        <br />
        <div style="width: 100%;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="font-size: 5px; border: 1px solid black; padding: 4px 2px;">
                            <p>م</p>
                            <p>No</p>
                        </th>
                        <th style="font-size: 5px; border: 1px solid black; padding: 4px 2px;">
                            <p>رقم الحجز</p>
                            <p>Reservation Number</p>
                        </th>
                        <th style="font-size: 5px; border: 1px solid black; padding: 4px 2px;" colspan="2">
                            <p>بيانات الفاتورة</p>
                            <p>Invoice Data</p>
                        </th>
                        <th style="font-size: 5px; border: 1px solid black; padding: 4px 2px;">
                            <p>عدد أيام الإقامة</p>
                            <p>No. of Days of Stay</p>
                        </th>
                        <th style="font-size: 5px; border: 1px solid black; padding: 4px 2px;">
                            <p>قيمة اليوم</p>
                            <p>Today's Value</p>
                        </th>
                        <th style="font-size: 5px; border: 1px solid black; padding: 4px 2px;">
                            <p>الاجمالي المبلغ قبل الضريبة</p>
                            <p>Total Amount Before Tax</p>
                        </th>
                        <th style="font-size: 5px; border: 1px solid black; padding: 4px 2px;">
                            <p>قيمة الضريبة 15%</p>
                            <p>Tax Amount 15%</p>
                        </th>
                        <th style="font-size: 5px; border: 1px solid black; padding: 4px 2px;">
                            <p>الإجمالي شامل الضريبة</p>
                            <p>Total Including Tax</p>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>

                        <td style="border: 1px solid black; padding: 2px;">{{  $booked->order_payment->invoice_number}}</td>
                        <td style="border: 1px solid black; padding: 2px;">{{  $booked->id}}</td>
                        <td style="border: 1px solid black; padding: 2px;"></td>
                        <td style="border: 1px solid black; padding: 2px;"></td>
                        <td style="border: 1px solid black; padding: 2px;">{{  Carbon\Carbon::parse($booked->date_from)->diffInDays(Carbon\Carbon::parse($booked->date_to)) }}</td>
                        <td style="border: 1px solid black; padding: 2px;">{{  $booked->price_day??$booked->apartment->price}}</td>
                        <td style="border: 1px solid black; padding: 2px;">{{  $booked->price_notax}}</td>
                        <td style="border: 1px solid black; padding: 2px;">{{  $booked->tax}}</td>
                        <td style="border: 1px solid black; padding: 2px;">{{  $booked->total_price}}</td>
                    </tr>
                </tbody>
            </table>

        </div>
        <br />
        <br />
        <table style="width: 100%;">
            <tr>

                <td style="width: 80%">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tbody>
                            <tr>
                                <td style="border: 1px solid black; padding: 2px;">ر.س -{{  $booked->price_notax}} </td>
                                <td style="border: 1px solid black; padding: 2px;">الاجمالي قبل الضريبة</td>
                                <td style="border: 1px solid black; padding: 2px;">Total Before Tax</td>
                                <td rowspan="3" style="border: 1px solid black; padding: 2px;">
                                    <p>فاتورة مبيعات Invoice Sales</p>
                                    <br />
                                    <div
                                        style="width: 100%; margin-bottom: 10px; text-align: center;">
                                        <p style="display: inline;">ملاحظات</p>
                                        <p style="display: inline; margin-right: 10%;">Notes</p>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black; padding: 2px;"> {{ $booked->tax }}</td>
                                <td style="border: 1px solid black; padding: 2px;">ضريبة القيمة الضريبة 15%</td>
                                <td style="border: 1px solid black; padding: 2px;">VAT 15%</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black; padding: 2px;">ر.س -{{  $booked->total_price}} </td>
                                <td style="border: 1px solid black; padding: 2px;">الإجمالي شامل الضريبة</td>
                                <td style="border: 1px solid black; padding: 2px;">Total Including Tax</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black; padding: 2px;">ر.س -{{ ( $booked->price_notax * $booked->tax / 100) }}  </td>
                                <td style="border: 1px solid black; padding: 2px;">مبلغ الخصم</td>
                                <td style="border: 1px solid black; padding: 2px;">Discount Amount</td>
                                <td rowspan="2" style="border: 1px solid black; padding: 2px;">
                                    <div
                                        style="width: 100%; margin-bottom: 10px; text-align: center;">
                                        <p style="display: inline;">Riyal, Nothing Else</p>
                                        <p style="display: inline; margin-right: 10%;">فقط</p>
                                    </div>
                                    <div
                                        style="width: 100%; margin-bottom: 10px; text-align: center;">
                                        <p style="display: inline;">Only One</p>
                                        <p style="display: inline; margin-right: 10%;"> ريال فقط لاغير</p>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black; padding: 2px;">ر.س -{{  $booked->order_payment->price}} </td>
                                <td style="border: 1px solid black; padding: 2px;">المبلغ المستحق</td>
                                <td style="border: 1px solid black; padding: 2px;">Total Amount Due</td>
                            </tr>
                        </tbody>
                    </table>

                </td>
            </tr>
        </table>
    {{-- </div> --}}

</body>

</html>
