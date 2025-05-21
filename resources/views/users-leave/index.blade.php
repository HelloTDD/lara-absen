<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Salary leaves</title>
</head>

<body>
    <form action="" method="post">
        @csrf
        <div>
            <label>Tanggal Mulai Cuti</label>
            <input type="date" name="start_date" id="start_date" required>
        </div>
        <div>
            <label>Tanggal Selesai Cuti</label>
            <input type="date" name="end_date" id="end_date" required>
        </div>
        <div>
            <label>Keterangan Cuti</label>
            <textarea name="description" id="description" required rows="10" maxlength="1000"></textarea>
        </div>
        <button type="submit">Submit</button>
    </form>
</body>

</html>