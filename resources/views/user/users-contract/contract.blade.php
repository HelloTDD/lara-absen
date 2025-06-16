<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perjanjian Kerja Waktu Tertentu (PKWT)</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            line-height: 1.6;
            background-color: #f9f9f9;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .company-name {
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 5px;
        }
        .contract-title {
            font-weight: bold;
            font-size: 16px;
            margin: 20px 0;
            text-decoration: underline;
        }
        .section {
            margin: 20px 0;
        }
        .section-title {
            font-weight: bold;
            background-color: #f0f0f0;
            padding: 8px;
            margin: 15px 0 10px 0;
            border-left: 4px solid #333;
        }
        .party-info {
            background-color: #f8f8f8;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .clause {
            margin: 15px 0;
            padding-left: 20px;
        }
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ccc;
        }
        .signature-box {
            text-align: center;
            width: 45%;
        }
        ol, ul {
            padding-left: 25px;
        }
        .work-schedule {
            background-color: #e8f4f8;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="company-name">PT. Maju Bersama Sejahtera</div>
            <div>Rukan Sentra Bisnis Artha Gading, Blok S No. 12-13A, Kelapa Gading Barat, DKI Jakarta 14241</div>
            <div>Phone: 021-12345678</div>
            <div style="border-top: 1px solid #000; margin: 10px 0;"></div>
            <div class="contract-title">PERJANJIAN KERJA WAKTU TERTENTU ( PKWT )</div>
            <div>Nomor: 090/HRD/PKWT/VII/2018</div>
        </div>

        <div class="section">
            <p>Yang bertanda tangan di bawah ini:</p>
            
            <div class="party-info">
                <div style="display: flex; margin-bottom: 10px;">
                    <div style="width: 150px;"><strong>I. Nama</strong></div>
                    <div>: Puji Astuti</div>
                </div>
                <div style="display: flex; margin-bottom: 10px;">
                    <div style="width: 150px;">Jabatan</div>
                    <div>: DIREKTUR</div>
                </div>
                <div style="display: flex; margin-bottom: 10px;">
                    <div style="width: 150px;">Perusahaan</div>
                    <div>: PT. Maju Bersama Sejahtera</div>
                </div>
                <div style="display: flex;">
                    <div style="width: 150px;">Alamat</div>
                    <div>: Rukan Sentra Bisnis Artha Gading, Blok S No. 12-13A, Kelapa Gading</div>
                </div>
            </div>

            <p>Dalam hal ini bertindak untuk dan atas nama <strong>PT. Maju Bersama Sejahtera</strong>, yang selanjutnya disebut sebagai <strong>PIHAK PERTAMA</strong></p>

            <div class="party-info">
                <div style="display: flex; margin-bottom: 10px;">
                    <div style="width: 150px;"><strong>II. Nama Lengkap</strong></div>
                    <div>: {{$data->contracts?->name}}</div>
                </div>
                <div style="display: flex; margin-bottom: 10px;">
                    <div style="width: 150px;">No. KTP/SIM</div>
                    <div>: 1234567891235</div>
                </div>
                <div style="display: flex; margin-bottom: 10px;">
                    <div style="width: 150px;">Tempat, Tgl. Lahir</div>
                    <div>: {{ \Carbon\Carbon::parse($data->contracts?->user?->birth_date)->format('d-m-Y') }}</div>
                </div>
                <div style="display: flex; margin-bottom: 10px;">
                    <div style="width: 150px;">Alamat</div>
                    <div>: {{$data->contracts?->user?->address}}</div>
                </div>
                <div style="display: flex; margin-bottom: 10px;">
                    <div style="width: 150px;">Telepon/HP</div>
                    <div>: {{$data->contracts?->user?->phone}}</div>
                </div>
                <div style="display: flex;">
                    <div style="width: 150px;">Email</div>
                    <div>: {{$data->contracts?->user?->email}}</div>
                </div>
            </div>

            <p>Dalam hal ini bertindak untuk dan atas nama pribadi, yang untuk selanjutnya disebut <strong>PIHAK KEDUA</strong></p>
        </div>

        <div class="section">
            <p>Pada hari ini, tanggal Satu, bulan Delapan, tahun Dua Ribu Delapan Belas <strong>(01-08-2018)</strong>, kedua belah pihak secara sadar sepakat mengadakan perjanjian kontrak kerja, dengan hal sebagai berikut:</p>
        </div>

        <div class="section-title">Pasal 1<br>KETENTUAN UMUM</div>
        <div class="clause">
            <ol>
                <li>Dengan ditandatanganinya Perjanjian Kerja ini berarti <strong>PIHAK KEDUA</strong> telah mengetahui dan setuju terhadap Peraturan Perusahaan atau peraturan-peraturan lain yang berlaku di Perusahaan.</li>
                <li>Demi kepentingan <strong>PIHAK PERTAMA</strong> dalam hal pengaturan kerja lembar maka <strong>PIHAK KEDUA</strong> menyepakati tugas yang diberikannya untuk memenuhi pekerjaan sesuai dengan yang berlaku.</li>
            </ol>
        </div>

        <div class="section-title">Pasal 2<br>PENUNJUKAN SEBAGAI KARYAWAN</div>
        <div class="clause">
            <ol>
                <li><strong>PIHAK PERTAMA</strong> menunjuk pekerjaan kepada <strong>PIHAK KEDUA</strong> dan <strong>PIHAK KEDUA</strong> mengaku menerima pekerjaan dari <strong>PIHAK PERTAMA</strong>.</li>
                <li>Dalam perjanjian kontrak kerja ini, <strong>PIHAK KEDUA</strong> melaksanakan pekerjaan sebagai <strong>Senior Sales & Marketing</strong> di lokasi <strong>PIHAK PERTAMA</strong> yang beralamat di Rukan Sentra Bisnis Artha Gading, Blok S No. 12-13A, Kelapa Gading Barat, Daerah Khusus Ibukota Jakarta.</li>
                <li>Pekerjaan sebagaimana disebut pada ayat 2 (dua) pasal ini dilaksanakan oleh <strong>PIHAK KEDUA</strong> mulai tanggal 1 (satu) bulan, bertihun muda sesuai yang tercantum.</li>
                <li>Masa berlaku kontrak kerja <strong>PIHAK KEDUA</strong> adalah mulai tanggal Satu, bulan Delapan, tahun Dua Ribu Sembilan Belas <strong>(01-08-2019)</strong>.</li>
                <li>Apabila masa kontrak kerja telah berakhir tanggal tersebut di atas, maka kontrak kerja berakhir tanpa ada kewajiban <strong>PIHAK PERTAMA</strong> memberitahu uang pesangon, uang jasa dan uang ganti rugi serta tunjangan lainnya kepada <strong>PIHAK KEDUA</strong>.</li>
                <li>Selama masa berlakunya kontrak, <strong>PIHAK KEDUA</strong> dapat sewaktu-waktu mengundurkan diri dengan pemberitahuan tertulis paling lambat 1 (satu) bulan sebelum <strong>PIHAK PERTAMA</strong> menghendaki <strong>PIHAK PERTAMA</strong> dapat sewaktu-waktu memutuskan perjanjian ini secara sepihak dan memberitahukan kepada <strong>PIHAK KEDUA</strong>.</li>
                <li>Dalam waktu selambat-lambatnya 7 (tujuh) hari kerja menjelang berakhirnya masa kontrak <strong>PIHAK PERTAMA</strong> dan <strong>PIHAK KEDUA</strong> wajib melakukan pembicaraan tentang <strong>PIHAK KEDUA</strong>.</li>
            </ol>
        </div>

        <div class="section-title">Pasal 3<br>HAK DAN KEWAJIBAN</div>
        <div class="clause">
            <ol>
                <li><strong>PIHAK PERTAMA</strong> dan <strong>PIHAK KEDUA</strong> secara bersama-sama berkewajiban mematasi hubungan kerja yang baik agar tercapai ketenangan dan ketentaraman dalam usaha.</li>
                <li><strong>PIHAK KEDUA</strong> berkewajiban memberikan laporan yang diperlukan oleh <strong>PIHAK PERTAMA</strong> seagaimana diatur Surat Perjanjian Kerja ini.</li>
                <li>Mengikuti jemput sosial tenaga kerja dan kesehatan dari <strong>PIHAK PERTAMA</strong>.</li>
                <li><strong>PIHAK KEDUA</strong> menjalankan tugas berdasarkan penetapan yang telah ditetapkan dalam pelaksanaan Job description yang menjadi tanggung jawab dari perjanjian ini.</li>
                <li>Memberikan update informasi mengenai penjualan, serta area yang dijadikankemanya - baik karena jabatannya, atau karena sebab lain - baik selama ia bekerja maupun sesudah Perjanjian Kerja ini berakhir.</li>
                <li>Segala perubahan terhadap sebagian atau seluruh isi perjanjian ini yang terjadi dikemudian hari - baik karena jabatannya, atau karena sebab lain termasuk semua informasi mancun dari klien, hard copy, email, dosel, CD, USB maupun dalam bentuk media lainnya, kepada divestasi.</li>
            </ol>
        </div>

        <div class="section-title">Pasal 4<br>SANKSI</div>
        <div class="clause">
            <ol>
                <li>Bila <strong>PIHAK KEDUA</strong> ternyata tidak memenuhi kewajiban-kewajiban tersebut diatas, maka <strong>PIHAK PERTAMA</strong> dapat memberikan tegaran atau ganti rugi menurut keputusannya.</li>
                <li>Apabila <strong>PIHAK KEDUA</strong> tidak mengindahkan teguren atau pemerintah tersebut, maka pemutusan akan dilakukan dengan muka kontrak kerja ini oleh <strong>PIHAK PERTAMA</strong> tanpa memberikan uang pesangon, uang jasa, ataupun uang ganti rugi kerugian lainnya kepada <strong>PIHAK KEDUA</strong>.</li>
            </ol>
        </div>

        <div class="section-title">Pasal 5<br>WAKTU DAN TEMPAT KERJA</div>
        <div class="clause">
            <p><strong>PIHAK KEDUA</strong> wajib menaati waktu kerja sebagai berikut:</p>
            <div class="work-schedule">
                <div style="display: flex; margin-bottom: 5px;">
                    <div style="width: 150px;">Senin - Jumat</div>
                    <div>: Jam 08.30 - 17.00 WIB</div>
                </div>
                <div style="display: flex;">
                    <div style="width: 150px;">Istirahat</div>
                    <div>: Jam 12.00 - 13.00 WIB</div>
                </div>
            </div>
            <p><em>*) sesuai dengan ditemukan dengan jadwal (*) Peraturan Perusahaan</em></p>
        </div>

        <div class="section-title">Pasal 6<br>PENYELESAIAN PERSELISIHAN</div>
        <div class="clause">
            <ol>
                <li>Bila terjadi perselisihan antara kedua belah pihak dalam melaksanakan perjanjian kerja ini, kedua belah pihak akan menyelesaikannya secara musyawarah.</li>
                <li>Apabila penyelesaikan pada ayat satu di atas tidak berhasil, maka perselisihan akan diselesaikan oleh Badan Arbitrase Nasional Indonesia (BANI).</li>
            </ol>
        </div>

        <div class="section-title">Pasal 7<br>LAIN-LAIN</div>
        <div class="clause">
            <ol>
                <li>Hal-hal yang belum tercantum di dalam Perjanjian ini, akan diatur kemudian.</li>
                <li>Segala perubahan terhadap sebagian atau seluruh isi-isi pasal dalam Perjanjian Kerja ini hanya dapat dilakukan dengan persetujuan kedua belah pihak.</li>
                <li>Perjanjian ini dibuat bersama-sama dan mengikat kedua belah pihak.</li>
            </ol>
        </div>

        <div class="section">
            <p>Demikianlah Perjanjian Kerja Waktu Tertentu ini dibuat oleh kedua belah pihak dalam keadaan sadar sehat jasmani dan rohani tanpa adanya paksaan atauupun tekanan dari pihak manapun.</p>
        </div>

        <div class="signature-section">
            <div class="signature-box">
                <p><strong>PIHAK PERTAMA</strong></p>
                <br><br><br>
                <p>________________________</p>
                <p><strong>(PIHAK PERTAMA)</strong></p>
            </div>
            <div class="signature-box">
                <p><strong>PIHAK KEDUA</strong></p>
                <br><br><br>
                <p>________________________</p>
                <p><strong>(PIHAK KEDUA)</strong></p>
            </div>
        </div>
    </div>
</body>
</html>