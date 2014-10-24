<div id="modalFormCreateEdit" style="display: none">
    <div id="div_Clonar" class="divClonar">

        <table class="tablaClonar"  >
            <tr>
                <td colspan="6" class="td-cabecera">
                    <strong>Datos de Grupo</strong>
                    <input type="hidden" id="idExistente" />
                </td>
            </tr>
            <tr>
                <td class="celda_titulo">Grupo:</td>
                <td class="celda_res"  colspan="2"><input type="text" name="grupo" id="grupo" class="caja_texto3"/></td>
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
                <td class="celda_res" colspan="6" align="center">
                    <button id="btn_guardar" class="action blue" title="Generar Password" >
                        <span class="label">Guardar Grupo</span>
                    </button>
                    <button id="btn_salir" class="action red" title="Cancelar">
                        <span class="label">Salir</span>
                    </button>
                </td>
            </tr>
        </table>
    </div>
</div>



