<h1>Reporte de Timesheets</h1>
<table width="100%" border="1" cellspacing="0" cellpadding="5">
    <thead>
        <tr>
            <th>Calendar</th>
            <th>Usuario</th>
            <th>Tipo</th>
            <th>Día de entrada</th>
            <th>Día de salida</th>
        </tr>
    </thead>
    <tbody>
        @foreach($timesheets as $t)
            <tr>
                <td>{{ $t->calendar->name }}</td>
                <td>{{ $t->user->name }}</td>
                <td>{{ $t->type }}</td>
                <td>{{ $t->day_in }}</td>
                <td>{{ $t->day_out }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
