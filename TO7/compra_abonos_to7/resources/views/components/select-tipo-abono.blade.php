<!-- VISTA DE UN COMPONENTE 
definido con la clase /app/View/Components/SelectTipoAbono.php -->
<!-- usado en compra.blade.php -->


                            <div class="col-md-5">
                                <label for="abono" class="form-label">Tipo de abono</label>
                                <select id="abono" name="abonoTipo" class="form-select">
                                    <option value="" disabled hidden @selected(old('abonoTipo') == "")>-</option>

                                    <!-- 
                                    $listado -- atributo de la clase SelectTipoAbono, $listado es un array de eloquent models TipoAbono
                                    $selectTipo es el atributo que se le pasa desde una vista, el old('campo') 
                                    -->
                                    @foreach($listado as $op)                        
                                    <option value="{{ $op->id }}" @selected($selectTipo == $op->id)>
                                    <!-- @//selected(condicion) -- imprime el atr selected solo si se cumple la condición. -->
                                        {{ $op->descripcion }}
                                    </option>
                                    @endforeach
                                </select>

                                @error('abonoTipo')
                                <p class="error">{{ $message }}</p>
                                @enderror
                            </div>