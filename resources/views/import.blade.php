<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    <form action="{{ route('cash-accounts.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" required class="border p-2">
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Import Excel</button>
    </form>
</body>

</html>
