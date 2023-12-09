<table class="w-100 ">
    <tbody>
        <tr>
            <td style="width: 12%;" class="">
                <img class="img-fluid" src="{{ $logo_link }}" alt="{{ $company->name }}">
            </td>
            <td class=" text-center">
                <h1 class="h3 ">{{ $company->name }}</h1>
                <p class="mt-1">Address: {{ $company->address }}, {{ $company->p_o_box }}</p>
                <p class="mt-0 fs-12">Website: {{ $company->website }}, Email: {{ $company->email }}</p>
                <p class="mt-0 fs-12">Tel: <b>{{ $company->phone_number }}</b> , <b>{{ $company->phone_number_2 }}</b>
                </p>
            </td>
            <td style="width: 8%;"><br></td>
        </tr>
    </tbody>
</table>
<hr style="border-width: 4px; color: {{ $company->color }}; border-color: {{ $company->color }};" class="mt-3 mb-1">
<hr style="border-width: 3px; color: black; border-color: black;" class="mb-3 mt-0">
