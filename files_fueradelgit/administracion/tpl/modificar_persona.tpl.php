<div id="modalFormCrearPersona" style="display: none">
    <div id="div_Clonar" class="divClonar">

        <table class="tablaClonar"  >
            <tr>
                <td colspan="6" class="td-cabecera">
                    <strong>Datos Personales</strong>
                    <input type="hidden" id="idpersona" />
                </td>
            </tr>
            <tr>
                <td class="celda_titulo">Apellido Paterno:</td>
                <td class="celda_res"  colspan="2"><input type="text" name="ape_pa" id="ape_pa" class="caja_texto3"/></td>
                <td class="celda_titulo">Apellido Materno:</td>
                <td class="celda_res"  colspan="2"><input type="text" name="ape_ma" id="ape_ma" class="caja_texto3"/></td>
            </tr>
            <tr>
                <td class="celda_titulo">Nombre:</td>
                <td class="celda_res"  colspan="2"><input type="text" name="nom" id="nom" class="caja_texto3"/></td>
                <td class="celda_titulo">DNI:</td>
                <td class="celda_res"  colspan="2"><input type="text" name="txtDni" id="dni" class="caja_texto3"/></td>
            </tr>
            <tr>
                <td class="celda_titulo">Empresa :</td>
                <td class="celda_res"  colspan="2">
                    <select name="empresa_principal" id="empresa_principal">
                        <?= $empresas_options_html; ?>
                    </select>
                </td>

                <td class="celda_titulo">Grupo:</td>
                <td class="celda_res"  colspan="2">
                    <select name="grupo" id="grupo"    multiple>
                        <?= $grupos_options_html; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="celda_titulo">Estado:</td>
                <td class="celda_res"  colspan="2">
                    <select name="estado" id="estado">
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="6" class="td-cabecera">
                    <strong>Datos de Contacto</strong>
                </td>
            </tr>
            <tr>
                <td colspan="6">
                    <a href="#" id="agregarNumContact"> [+ Agregar Numero]</a>
                </td>
            </tr>
            <tr>
                <td colspan="6">
                    <table id="tableContactoPersona">
                        <tr class="cabecera">
                            <th>Numero</th>
                            <th>Por Defecto</th>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="celda_res" colspan="6" align="center">
                    <button id="btn_guardar" class="action blue" title="Generar Password" >
                        <span class="label">Guardar persona</span>
                    </button>
                    <button id="btn_salir" class="action red" title="Cancelar">
                        <span class="label">Salir</span>
                    </button>
                </td>
            </tr>
        </table>
    </div>
</div>

<script id="NumeroContactoPersona" type="text/template">

    <tr class="numContact-row row-<%= num_row %>" >
        <td>
            <input type="text" class= "numcontact" id="nc<%= num_row %>" row="<%= num_row %>" value="<%= numero %>"/>
        </td>
        <td>
            <input type="radio"  class="numcontactrb" id="rb<%= num_row %>" name="numcontactrb" row=""  value="1" <%= checked %>>
        </td>
        <td>
            <span class="remove-row">[X]</span>
        </td>
    </tr>

</script>


