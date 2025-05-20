<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    <form action="{{ route('user-salaries.store') }}" method="post">
        @csrf
        <div>
            <label for="user_id">User</label>
            <select name="user_id" id="user_id" required>
                @if (count($users) == 0)
                    <option value="">No users available</option>
                @else
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                @endif
            </select>
        </div>
        <div>
            <label for="salary_basic">Basic Salary</label>
            <input type="number" name="salary_basic" id="salary_basic" value="0" required>
        </div>
        <div>
            <label for="salary_allowance">Allowance</label>
            <input type="number" name="salary_allowance" id="salary_allowance" value="0" required>
        </div>
        <div>
            <label for="salary_bonus">Bonus</label>
            <input type="number" name="salary_bonus" id="salary_bonus" value="0" required>
        </div>
        <div>
            <label for="salary_holiday">Holiday</label>
            <input type="number" name="salary_holiday" id="salary_holiday" value="0" required>
        </div>
        <button type="submit">Submit</button>
    </form>
</body>

</html>