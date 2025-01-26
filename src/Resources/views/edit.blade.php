@section('title', '')

@section('content')

    <form action="" method="POST">
        @csrf
        @method('PUT')
        @include('crud::partials.form')

        <button type="submit">
            Update
        </button>
    </form>

@endsection