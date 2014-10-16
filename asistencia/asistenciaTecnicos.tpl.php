
<!--Template reusable usando juntos con la libreria underscore.js-->
<script id="TecnicoDetalleAsistencia" type="text/template">

    <div class="cabecera">
        <div class="empleado">
            <span>Empleado : <%= carnet %>  <%= nombre %> <%= celula %></span>
        </div>
    </div>
    <table class="asisDetalle">
        <tr>
            <th>Fecha</th>
            <th>Tipo Entrada</th>
            <th>Direccione</th>
            <th>Info Adicional</th>
        </tr>
        <% _.each(asistencias,function(item){ %>
            <tr>
                <td><%= item.fecha %></td>
                <td><%= item.entrada %></td>
                <td><%= item.direccion %></td>
                <td><%= item.descripcion %></td>
            </tr>
        <% }) %>
    </table>

</script>

