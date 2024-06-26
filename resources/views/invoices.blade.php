<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Simplified Tax Invoice</title>
  {{-- <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet"> --}}

  <link  href="{{ asset('dist/output.css') }}" rel="stylesheet">
  <!-- ICONS  -->



 <style>
    @media print {
      @page {

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
    .whitespace-nowrap{
        color: red;
    }
  </style>
</head>

@php
$invoice =  \App\Models\Invoice::find(1);
 @endphp
<body dir="rtl" style="font-size: 6px;">
  <div class="container mx-auto p-8">
    <div class="flex justify-center mb-8">
      <div class="w-full">
        <div class="flex mb-4 font-bold relative">
          <div class="h-20 mx-auto mb-4 absolute" style="right: 50%; top: -10px; transform: translateX(50%);">
            <img class="w-full h-full" src="{{asset('uploads/invoice/' . $invoice->img)  }}" alt="">
          </div>

          <div class="w-1/2 flex flex-col overflow-hidden">
            <p class="mb-1" style="font-size: 8px;"></p>{{   $invoice->title?? 'شركة قطون للسفر والسياحة'}}</p>

            <div class="flex gap-4">
              <ul>
                <li class="whitespace-nowrap " style="color: red">هاتف</li>
                <li class="whitespace-nowrap">فاكس</li>
                <li class="whitespace-nowrap">الرقم الضريبي</li>
                <li class="whitespace-nowrap">العنوان</li>
                <li class="whitespace-nowrap">جوال</li>
                <li class="whitespace-nowrap">البريد الالكتروني</li>
                <li class="whitespace-nowrap">رقم السجل التجاري</li>
              </ul>
              <ul>
                <li class="whitespace-nowrap">{{  $invoice->mobile}}</li>
                <li class="whitespace-nowrap">{{  $invoice->fax}}</li>
                <li class="whitespace-nowrap">{{  $invoice->tax_number}}</li>
                <li class="whitespace-nowrap">{{  $invoice->address_ar}}</li>
                <li class="whitespace-nowrap">{{  $invoice->phone}}</li>
                <li class="whitespace-nowrap">{{  $invoice->email}}</li>
                <li class="whitespace-nowrap">{{  $invoice->CRN}}</li>
              </ul>
            </div>
          </div>
          <div class="w-1/2 flex flex-col ltr overflow-hidden">
            <p class="mb-1" style="font-size: 8px;">Co .T .Q</p>
            <div class="flex gap-4">
              <ul>
                <li class="whitespace-nowrap">Phone</li>
                <li class="whitespace-nowrap">Fax</li>
                <li class="whitespace-nowrap">Tax Number</li>
                <li class="whitespace-nowrap">Address</li>
                <li class="whitespace-nowrap">C. Phone</li>
                <li class="whitespace-nowrap">E-Mail</li>
                <li class="whitespace-nowrap">Commercial Reg.</li>
              </ul>
              <ul>
                <li class="whitespace-nowrap">{{  $invoice->mobile}}</li>
                <li class="whitespace-nowrap">{{  $invoice->fax}}</li>
                <li class="whitespace-nowrap">{{  $invoice->tax_number}}</li>
                <li class="whitespace-nowrap">{{  $invoice->address_en}}</li>
                <li class="whitespace-nowrap">{{  $invoice->phone}}</li>
                <li class="whitespace-nowrap">{{  $invoice->email}}</li>
                <li class="whitespace-nowrap">{{  $invoice->CRN}}</li>
              </ul>
            </div>
          </div>
        </div>

        <hr class="mt-2 mb-4" />

        <div class="flex mb-4 gap-4">
          <div class="w-1/2 rtl">
            <div class="w-20 h-20 mx-auto mb-4">
                {!! QrCode::generate(route('show-invoice', [$booked->order_payment->invoice_number])) !!}
            </div>
            <div class="rtl">
              <table class="table-fixed border-collapse border border-slate-500 w-full mb-6">
                <thead class="bg-gray-200 text-gray-700">
                  <tr>
                    <th class="border border-slate-600" colspan="2" style="font-size: 5px;">بيانات العميل</th>
                    <th class="border border-slate-600 p-2 text-left">Customer Data</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td class="border border-slate-700 p-1">اسم العميل</td>
                    <td class="border border-slate-700 p-1">{{  $booked->user->name }}</td>
                    <td class="border border-slate-700 p-1 text-left">Customer Name</td>
                  </tr>
                  <tr>
                    <td class="border border-slate-700 p-1">طريقة الدفع</td>
                    <td class="border border-slate-700 p-1"> Tabby /Fatoorah  </td>
                    <td class="border border-slate-700 p-1 text-left">Payment Method</td>
                  </tr>
                  <tr>
                    <td class="border border-slate-700 p-1">تاريخ الدفع</td>
                    <td class="border border-slate-700 p-1">
                      <pre>  {{  $booked->order_payment->created_at }}</pre>
                    </td>
                    <td class="border border-slate-700 p-1 text-left">Payment Date</td>
                  </tr>
                  <tr>
                    <td class="border border-slate-700 p-1">تسجيل الوصول</td>
                    <td class="border border-slate-700 p-1">
                      <pre>  {{   Carbon\Carbon::parse($booked->date_from)  }}</pre>
                    </td>
                    <td class="border border-slate-700 p-1 text-left">Check-in</td>
                  </tr>
                  <tr>
                    <td class="border border-slate-700 p-1">تسجيل المغادرة</td>
                    <td class="border border-slate-700 p-1">
                      <pre>   {{  Carbon\Carbon::parse($booked->date_to)  }}</pre>
                    </td>
                    <td class="border border-slate-700 p-1 text-left">Check-out</td>
                  </tr>
                  <tr>
                    <td class="border border-slate-700 p-1">عدد الليالي المدفوعة</td>
                    <td class="border border-slate-700 p-1">{{   Carbon\Carbon::parse($booked->date_from)->diffInDays(Carbon\Carbon::parse($booked->date_to)) }}</td>
                    <td class="border border-slate-700 p-1 text-left">Number of Paid Nights</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="w-1/2 ltr">
            <div dir="rtl" class="mb-2">
              <p class="bg-gray-200 text-gray-700 px-2 py-1" style="width: fit-content; font-size: 8px; font-weight: bold;">فاتورة مبيعات رضيبة مبسطة</p>
              <p class=" px-2" style=" font-size: 8px; font-weight: bold;"> Simplified tax invoice</p>
            </div>
            <table class="table-fixed border-collapse border border-slate-500 w-full mb-6 rtl">
              <tbody>
                <tr>
                  <td class="border border-slate-700 p-1">تاريخ اصدار الفاتورة:
                    <pre class="inline">  {{  $booked->order_payment->updated_at}}</pre>
                  </td>
                  <td class="border border-slate-700 p-1 text-left" dir="ltr">Invoice Issuance Date:
                    <pre class="inline">{{  $booked->order_payment->updated_at}} </pre>
                  </td>
                </tr>
                <tr>
                  <td class="border border-slate-700 p-1">رقم الفاتورة : {{  $booked->order_payment->invoice_number}}</td>
                  <td class="border border-slate-700 p-1 text-left" dir="ltr">Invoice Number: {{  $booked->order_payment->invoice_id}}</td>
                </tr>
              </tbody>
            </table>
            <table class="table-fixed border-collapse border border-slate-500 w-full rtl">
              <thead class="bg-gray-200 text-gray-700">
                <tr>
                  <th class="border border-slate-600 p-2" colspan="2">بيانات الشقة</th>
                  <th class="border border-slate-600 p-2 text-left">Apartment Data</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="border border-slate-700 p-1">اسم الحي</td>
                  <td class="border border-slate-700 p-1">{{  $booked->apartment->area->name}}</td>
                  <td class="border border-slate-700 p-1 text-left">District</td>
                </tr>
                <tr>
                  <td class="border border-slate-700 p-1">رقم العمارة</td>
                  <td class="border border-slate-700 p-1"></td>
                  <td class="border border-slate-700 p-1 text-left">Building No</td>
                </tr>
                <tr>
                  <td class="border border-slate-700 p-1">رقم الشقة</td>
                  <td class="border border-slate-700 p-1">{{  $booked->apartment->code}}</td>
                  <td class="border border-slate-700 p-1 text-left">Apartment No</td>
                </tr>
                <tr>
                  <td class="border border-slate-700 p-1">رقم الحجز</td>
                  <td class="border border-slate-700 p-1">{{  $booked->id}}</td>
                  <td class="border border-slate-700 p-1 text-left">Reservation No</td>
                </tr>
                <tr>
                  <td class="border border-slate-700 p-1">متوفر خدمة الإنترنت</td>
                  <td class="border border-slate-700 p-1">{{  !empty($booked->apartment->internet_name)?'متوفر':'غير متوفر'}}</td>
                  <td class="border border-slate-700 p-1 text-left">Internet Service is Available</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="flex flex-col mb-4">
          <p class="mx-auto mb-2 underline font-bold">تفاصيل الفاتورة - details Invoice</p>
          <div class="flex">
            <div class="w-full">
              <table class="table-fixed border-collapse border border-slate-500 w-full rtl">
                <thead class="bg-gray-200 text-gray-700">
                  <tr>
                    <th class="border border-slate-600 text-center w-4" style="font-size: 5px;">
                      <p>م</p>
                      <p>No</p>
                    </th>
                    <th class="border border-slate-600 text-center w-20" style="font-size: 5px;">
                      <p>رقم الحجز</p>
                      <p>Reservation Number</p>
                    </th>
                    <th class="border border-slate-600 text-center" style="font-size: 5px;" colspan="2">
                      <p>بيانات الفاتورة</p>
                      <p>Invoice Data</p>
                    </th>
                    <th class="border border-slate-600 text-center w-16" style="font-size: 5px;">
                      <p>عدد أيام الإقامة</p>
                      <p>No. of Days of Stay</p>
                    </th>
                    <th class="border border-slate-600 text-center w-16" style="font-size: 5px;">
                      <p>قيمة اليوم</p>
                      <p>Today's Value</p>
                    </th>
                    <th class="border border-slate-600 text-center w-16" style="font-size: 5px;">
                      <p>الاجمالي المبلغ قبل الضريبة</p>
                      <p>Total Amount Before Tax</p>
                    </th>
                    <th class="border border-slate-600 text-center w-16" style="font-size: 5px;">
                      <p>قيمة الضريبة 15%</p>
                      <p>Tax Amount 15%</p>
                    </th>
                    <th class="border border-slate-600 text-center w-16" style="font-size: 5px;">
                      <p>الإجمالي شامل الضريبة</p>
                      <p>Total Including Tax</p>
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td class="border border-slate-700 p-1 text-center">{{  $booked->order_payment->invoice_number}}</td>
                    <td class="border border-slate-700 p-1 text-center">{{  $booked->id}}</td>
                    <td class="border border-slate-700 p-1 text-center"></td>
                    <td class="border border-slate-700 p-1 text-center"></td>
                    <td class="border border-slate-700 p-1 text-center">{{  Carbon\Carbon::parse($booked->date_from)->diffInDays(Carbon\Carbon::parse($booked->date_to)) }}</td>
                    <td class="border border-slate-700 p-1 text-center">{{  $booked->price_day??$booked->apartment->price}}</td>
                    <td class="border border-slate-700 p-1 text-center">{{  $booked->price_notax}}</td>
                    <td class="border border-slate-700 p-1 text-center">{{  $booked->tax}}</td>
                    <td class="border border-slate-700 p-1 text-center">{{  $booked->total_price}}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="flex mt-4 ltr">
          <div class="w-3/4 rtl">
            <table class="table-fixed border-collapse border border-slate-500 w-full rtl">
              <tbody>
                <tr>
                  <td class="border border-slate-700 p-1">ر.س -{{  $booked->price_notax}} </td>
                  <td class="border border-slate-700 p-1">الاجمالي قبل الضريبة</td>
                  <td class="border border-slate-700 p-1 text-left">Total Before Tax</td>
                  <td class="border border-slate-700 p-1 text-center" rowspan="3">
                    <p">فاتورة مبيعات Invoice Sales</p>
                    <div class="flex gap-8">
                      <ul>
                        <li class="underline font-bold">ملاحظات</li>
                      </ul>
                      <ul>
                        <li class="underline font-bold">Notes</li>
                      </ul>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td class="border border-slate-700 p-1">ر.س -{{  $booked->price_notax}} </td>
                  <td class="border border-slate-700 p-1">ضريبة القيمة الضريبة 15%</td>
                  <td class="border border-slate-700 p-1 text-left">VAT 15%</td>
                </tr>
                <tr>
                  <td class="border border-slate-700 p-1">ر.س -{{  $booked->total_price}} </td>
                  <td class="border border-slate-700 p-1">الإجمالي شامل الضريبة</td>
                  <td class="border border-slate-700 p-1 text-left">Total Including Tax</td>
                </tr>
                <tr>
                  <td class="border border-slate-700 p-1">ر.س -{{ ( $booked->price_notax * $booked->tax / 100) }} </td>
                  <td class="border border-slate-700 p-1">مبلغ الخصم</td>
                  <td class="border border-slate-700 p-1 text-left">Discount Amount</td>
                  <td class="border border-slate-700 p-1 text-center" rowspan="2">
                    <div class="flex gap-4">
                      <ul>
                        <li>Riyal, Nothing Else</li>
                        <li>فقط</li>
                      </ul>
                      <ul>
                        <li>Only One</li>
                        <li>ريال فقط لاغير</li>
                      </ul>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td class="border border-slate-700 p-1">ر.س -{{  $booked->order_payment->price}} </td>
                  <td class="border border-slate-700 p-1">المبلغ المستحق</td>
                  <td class="border border-slate-700 p-1 text-left">Total Amount Due</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="flex">
          <p>الشروط ولاحكام:</p>
        </div>

      </div>
    </div>
  </div>
</body>

</html>

