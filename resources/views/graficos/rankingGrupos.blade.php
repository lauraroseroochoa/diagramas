<div class="row p-0 m-0" style="height: 69vh; max-height: 69vh; overflow:hidden; border:0px solid red;">
    <div class="col-9 m-0 p-0 me-3 ">
        <div class="row p-0 m-0">
            <div class="col-12 p-0 m-0  text-danger ">Ranking Grupos</div>
            <div class="col-12 m-0 p-0 me-2" style="height:80vh;">
                <table class="datatable2 table table-striped w-100" style="font-size: 12px;">
                    <thead>
                        <tr>
                            <th>Nombre del Grupo</th>
                            <th>Total Tradicional</th>
                            <th>Total LED</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ranking as $Grupos)
                            <tr>
                                <td>{{ $Grupos['nombre'] }}</td>
                                <td>{{ $Grupos['tradicional'] }}</td>
                                <td>{{ $Grupos['led'] }}</td>
                                <td>{{ $Grupos['total'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Total:</th>
                            <th id="totalTradicional"></th>
                            <th id="totalLed"></th>
                            <th id="totalGeneral"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var table2 = setupDataTable('datatable2');

function setupDataTable(selector) {
    var table = $('.' + selector).DataTable({
        paging: false,
        order: [[3, 'desc']], // Ordenar por la columna de "Total"
        dom: 'Brtp <"actions">',
        orderCellsTop: true,
        fixedHeader: true,
        scrollX: true,
        scrollY: "45vh",
        scrollCollapse: true,
        footerCallback: function (row, data, start, end, display) {
            var api = this.api();

            // Convierte los valores a enteros y realiza la suma
            var totalTradicional = api.column(1, { page: 'current' }).data().reduce(function (a, b) {
                return parseInt(a) + parseInt(b); // Convertir los valores a enteros antes de sumar
            }, 0);

            var totalLED = api.column(2, { page: 'current' }).data().reduce(function (a, b) {
                return parseInt(a) + parseInt(b); // Convertir los valores a enteros antes de sumar
            }, 0);

            var totalTotal = api.column(3, { page: 'current' }).data().reduce(function (a, b) {
                return parseInt(a) + parseInt(b); // Convertir los valores a enteros antes de sumar
            }, 0);

            // Muestra los totales en el pie de página sin decimales
            $(api.column(1).footer()).html(totalTradicional);
            $(api.column(2).footer()).html(totalLED);
            $(api.column(3).footer()).html(totalTotal);
        }
    });

    table.buttons().remove();
    addSearchInput(table, selector);

    return table;
}

function addSearchInput(table, selector) {
    $(selector + ' thead tr:eq(0) th').each(function(i) {
        $(this).append(
            '<input type="text" placeholder="Buscar: " style="width:100%; font-size:11px; line-height:15px; border:none; border:1px solid #f2f2f2;" />'
        );
        $('input', this).on('keyup change', function() {
            if (table.column(i).search() !== this.value) {
                table.column(i).search(this.value).draw();
            }
        });
    });
}

</script>
