<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Keterangan Kerja - PT. Transformasi Data Digital</title>
    <style>
       body {
            font-family: 'Times New Roman', serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            line-height: 1.6;
            color: #000;
            background-color: #fff;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px double #000;
            padding-bottom: 20px;
        }



        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin: 10px 0;
        }

        .company-branch {
            font-size: 14px;
            margin: 5px 0;
        }

        .company-address {
            font-size: 12px;
            margin: 5px 0;
        }

        .document-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            text-decoration: underline;
            margin: 30px 0 20px 0;
        }

        .document-number {
            text-align: center;
            font-size: 12px;
            margin-bottom: 30px;
        }

        .content {
            text-align: justify;
            font-size: 14px;
        }

        .section {
            margin: 20px 0;
        }

        .info-table {
            width: 100%;
            font-size: 14px;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .info-table td {
            padding: 4px 0;
            vertical-align: top;
        }

        .info-label {
            width: 160px;
            font-weight: normal;
        }

        .info-separator {
            width: 10px;
        }

        .info-value {
            width: auto;
        }

        .signature-section {
            margin-top: 50px;
            text-align: left;
        }

        .signature-date {
            margin-bottom: 10px;
        }

        .signature-company {
            margin-bottom: 60px;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }

        .signature-title {
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">PT. TRANSFORMASI DATA DIGITAL</div>
        <div class="company-address">
            Jl. Fajar Indah V No. C69, Jaten, Karanganyar, Jawa Tengah, 57731<br>
            Telp. +62 822-4400-5858
        </div>
    </div>

    <div class="document-title">SURAT KETERANGAN KERJA</div>
    <div class="document-number">No. {{ $data->references_no }}</div>

    <div class="content">
        <div class="section">
            Yang bertanda tangan di bawah ini :
            <table class="info-table">
                <tr>
                    <td class="info-label">Nama</td>
                    <td class="info-separator">:</td>
                    <td class="info-value">{{Str::ucfirst($data->approve_with ?? 'admin')}}</td>
                </tr>
                <tr>
                    <td class="info-label">Jabatan</td>
                    <td class="info-separator">:</td>
                    <td class="info-value"> HRD </td>
                </tr>
            </table>
        </div>

        <div class="section">
            Dengan ini menerangkan bahwa :

            <table class="info-table">
                <tr>
                    <td class="info-label">Nama</td>
                    <td class="info-separator">:</td>
                    <td class="info-value">{{ $data->user?->name }}</td>
                </tr>
                <tr class="info-row">
                    <td class="info-label">Tempat/Tgl. Lahir</td>
                    <td class="info-separator">:</td>
                    <td class="info-value">{{ $data->user?->address.', '.$data->user?->birth_date }}</td>
                </tr>
                <tr class="info-row">
                    <td class="info-label">Jabatan</td>
                    <td class="info-separator">:</td>
                    <td class="info-value">{{ $data->user?->role?->role_name }}</td>
                </tr>
            </table>
        </div>

        <div class="section">
            Adalah benar bekerja sebagai karyawan PT. Transformasi Data Digital di bagian {{ $data->userContract->position ?? 'Programmer' }},
            terhitung sejak tanggal {{ \Carbon\Carbon::parse($data->userContract->start_contract_date ?? now())->translatedFormat('d F Y') }} sampai dengan sekarang.
        </div>

        <div class="section">
            {{ $data->desc_references ?? 'Selama bekerja yang bersangkutan telah menunjukkan dedikasi dan loyalitas tinggi serta memberikan kontribusi positif bagi perusahaan.
            Selain itu, yang bersangkutan tidak pernah melakukan tindakan yang merugikan perusahaan.' }}
        </div>

        <div class="section">
            Demikian Surat Keterangan ini dibuat untuk dipergunakan sebagai bahan referensi atau dipergunakan sebagaimana mestinya.
        </div>

        <div class="signature-section">
            <div class="signature-date">Karanganyar, {{ \Carbon\Carbon::today()->format('d M Y') }}</div>
            <div class="signature-company">PT. Transformasi Data Digital</div>

            <div class="signature-name">{{ Str::ucfirst($data->approve_with ?? 'admin') }}</div>
            <div class="signature-title">HRD</div>
        </div>
    </div>
</body>
</html>
