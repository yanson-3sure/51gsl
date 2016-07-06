@foreach($questions as $question)
    <li>
        @include('ajax.qa.item')
    </li>
@endforeach