<x-filament-panels::page>

    @push('styles')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        @media print {
            body {
                font-family: '微軟正黑體', arial;
                -webkit-print-color-adjust: exact;
            }
            .print-content {
                height: 100%;
                page-break-after: always;
            }
            @page {
                size: A4 portrait;
                margin: 0;
            }
        }

        body {
            font-family: '微軟正黑體', arial;
            -webkit-print-color-adjust: exact;
        }
        .print-content {
            page-break-after: always;
        }
    </style>
    @endpush

    {{-- 列印按鈕 --}}
    <div class="max-w-4xl mx-auto mb-4 text-right print:hidden">
        <x-filament::button
            icon="heroicon-o-printer"
            href="javascript:void(0)"
            onclick="printContent()"
        >列印</x-filament::button>
        <x-filament::button
            icon="heroicon-o-arrow-down-tray"
            onclick="downloadPDF()"
        >下載(PDF)</x-filament::button>
    </div>

    {{-- 發票內容 --}}
    {{ $slot }}

    {{-- 列印功能 --}}
    <script>
        function printContent() {
            const content = document.getElementById('order-info-area').innerHTML;
            const original = document.body.innerHTML;

            document.body.innerHTML = content;

            // 等待所有圖片加載完成後再列印
            const images = document.images;
            let loaded = 0;

            for (let i = 0; i < images.length; i++) {
                images[i].onload = () => {
                    loaded++;
                    if (loaded === images.length) {
                        window.print();
                        document.body.innerHTML = original;
                    }
                };
                images[i].onerror = () => {
                    loaded++;
                    if (loaded === images.length) {
                        window.print();
                        document.body.innerHTML = original;
                    }
                };
            }
        }

        function downloadPDF() {
            const element = document.getElementById('order-info-area');
            html2pdf()
                .set({
                    margin: 0,
                    filename: 'order-'+{{date('YmdHis')}}+'.pdf',
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: { scale: 2 },
                    jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
                })
                .from(element)
                .save();
        }
    </script>

</x-filament-panels::page>
