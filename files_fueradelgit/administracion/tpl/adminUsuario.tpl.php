<div id="modalFormCrearUsuario" style="display: none">
    <div id="div_Clonar" class="divClonar">

        <table class="tablaClonar"  >
            <tr>
                <td colspan="6" class="td-cabecera">
                    <strong>Datos Personales</strong>
                        <input type="hidden" name="txtNombre" id="idusuario" class="caja_texto3"/>
                </td>
            </tr>
            <tr>
                <td class="celda_titulo">Nombre:</td>
                <td class="celda_res"  colspan="2"><input type="text" name="txtNombre" id="nom" class="caja_texto3"/></td>


                <td class="celda_titulo">Apellidos:</td>
                <td class="celda_res"  colspan="2"><input type="text" name="txtApellidos" id="ape" class="caja_texto3"/></td>
            </tr>
            <tr>
                <td class="celda_titulo">Login Usuario:</td>
                <td class="celda_res"  colspan="2"><input type="text" name="txtLogin" id="login" class="caja_texto3"/></td>

                <td class="celda_titulo">Password:</td>
                <td class="celda_res"><input type="text" name="txtPassword" id="pass" class="caja_texto3" readonly="readonly"/>
                </td>
                <td class="celda_res">
                    <button id="generarPass" class="action blue" title="Generar Password" style="width: 100px" >
                        <span class="label">Generar Password</span>
                    </button>
                </td>

                </td>
            </tr>
            <tr>
                <td class="celda_titulo">DNI:</td>
                <td class="celda_res"  colspan="2"><input type="text" name="txtDni" id="dni" class="caja_texto3"/></td>

                <td class="celda_titulo">Online:</td>
                <td class="celda_res"  colspan="2">
                    <select name="onlne" id="online">
                        <option value="0">Inactivo</option>
                        <option value="1">Activo</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="celda_titulo">Perfil:</td>
                <td class="celda_res"  colspan="2">
                    <select name="perfil" id="perfil">
                        <?= $perfiles_options_html; ?>
                    </select>
                </td>

                <td class="celda_titulo">Area:</td>
                <td class="celda_res"  colspan="2">
                    <select name="area" id="area">
                        <?= $areas_options_html; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="celda_titulo">Empresa Pricipal:</td>
                <td class="celda_res"  colspan="2">
                    <select name="empresa_principal" id="empresa_principal">
                        <?= $empresas_options_html; ?>
                    </select>
                </td>

                <td class="celda_titulo">Otras empresas:</td>
                <td class="celda_res"  colspan="2">
                    <select name="empresas" id="empresas" class="multiselect" style="display: none" multiple>
                        <?= $empresas_options_html; ?>
                    </select>
                </td>
            </tr>

            <tr>
                <td class="celda_titulo">Quiebres:</td>
                <td class="celda_res"  colspan="2">
                    <select name="quiebres" id="quiebres" multiple class="multiselect" >
                       <?=$quiebres_options_html;?>
                    </select>

                </td>

                <td class="celda_titulo">Estado:</td>
                <td class="celda_res"  colspan="2">
                    <select name="estado" id="estado">
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>

                </td>
            </tr>

            <tr>
                <td colspan="4"  class="td-cabecera">
                    <strong>Empresas Criticos</strong>
                </td>
                <td colspan="3"  class="td-cabecera">
                    <strong>Proyectos Asignados</strong>
                </td>
            </tr>
            <tr>
                <td colspan="4">
                    <table class="table-empresas-criticas">
                        <tr class="row-cabecera">
                            <th>Empresa Critica</th>
                            <th class="espacio-1">Activo</th>
                            <th class="espacio-1">Visible</th>
                        </tr>
                        <?= $empresas_criticas_trs; ?>
                    </table>
                </td>
                <td colspan="3">
                    <table class="table-proyectos" width="100%">

                        <tr class="row-cabecera">
                            <th>Proyectos</th>
                            <th class="espacio-1">Asignar</th>
                            <th class="espacio-1">Editable</th>
                        </tr>

                        <tr>
                            <td colspan="3">
                               <div style="overflow-y: scroll; height: 115px;">
                                   <table class="table-row-proy-selected" width="100%">
                                       <?= $proyectos_trs; ?>
                                   </table>
                               </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>



            <tr>
                <td colspan="6"  class="td-cabecera">
                    <strong>Permisos y accessos a los modulos</strong>
                </td>
            </tr>
            <tr>
                <td colspan="4">
                    <select name="modulos" id="modulos">
                        <?= $modulos_options_html;?>
                    </select>
                    <a href="#" id="agregarModulo">Agregar Modulo</a>
                </td>
                <td class="celda_res"  align="center">

                    <button id="btn_guardar" class="action blue" title="Generar Password" >
                        <span class="label">:: Guardar Usuario :: </span>
                    </button>
                    <button id="btn_salir" class="action red" title="Cancelar">
                        <span class="label"> :: Cancelar :: </span>
                    </button>

                </td>
            </tr>
            <tr>
                <td colspan="6" style="text-align: center">
                    <table id="listModulosSeleccionados" width="100%">
                        <thead>
                        <tr>

                            <th>Modulo</th>
                            <th>Submodulos</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tr>
                          <td colspan="3">
                              <table width="100%" id="newrowsadded"></table>
                          </td>
                      </tr>
                    </table>
                </td>
            </tr>

            <tr>

            </tr>

        </table>

    </div>
</div>

<script id="SubModuloTemplate" type="text/template">

       <tr class="submodulo-row">
           <td style="width:187px">
              <%= nombre %>
               <input type="hidden" class= "modulo selected" id="modulo_<%= id %>" id="<%= id %>" value="<%= id %>"/>
           </td>
           <td style="width:319px">
               <select name="submodulo_<%= id %>" class="submodulo" id="submodulo_<%= id %>" multiple>
               <% _.each(items,function(item,key,list){ %>
                 <option value="<%= item.idsubmodulo %>">  <%= item.submodulo %></option>
               <% }) %>
               </select>

           </td>
           <td>
               <span class="remove-row">[X]</span>
           </td>
       </tr>

</script>