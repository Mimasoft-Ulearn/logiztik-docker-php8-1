
/**
 * sheetjs_exportData
 * 
 * Método recibe el id de una tabla HTML. Procesa los datos por cada fila y columna.
 * Los valores de cada celda de la tabla HTML vienen como un string, por lo que,
 * para los valores numéricos, se debe formatear dicho string (por los puntos y comas),
 * esto para que en el archivo Excel exportado, queden como celdas con formato numérico.
 * 
 * @param id_table
 * @param file_name
 * @param file_type = "xlsx"
 * 
 * @author Gustavo Pinochet Altamirano
 */

function sheetjs_exportData(id_table, file_name, file_type = "xlsx"){

    var trs = document.querySelectorAll('#' + id_table + ' tr'); // Obtengo los tr de la tabla HTML

    var data = [];  // Arreglo para guardar los registros de la tabla HTML
    for(var tr of trs) {
        var th_td = tr.getElementsByTagName('td');
        if (th_td.length == 0) {
            th_td = tr.getElementsByTagName('th');
        }
        var th_td_array = Array.from(th_td); // convierte HTMLCollection a un Array
        th_td_array = th_td_array.map(tag => tag.innerText); // obtiene el texto de cada elemento
        data.push(th_td_array);
    }

    var data_final = []; // Arreglo para guardar los registros numéricos formateados de la tabla HTML
    //var regExp = /[a-zA-Z]/g; // Expresión regular que considera letras de la a-z y A-Z
    var regExp = /^[0-9,.]*$/; // Expresión regular que considera números, puntos y comas
    
    for(var row of data) {

        var array_row = []; // Arreglo para guardar una fila de datos correspondiente a la tabla HTML
        
        for(var dt of row) {
            // Si el dato es un número
            if(regExp.test(dt)){
                var index_coma = dt.lastIndexOf(","); // Obtener el index de la última coma
                var index_punto = dt.lastIndexOf("."); // Obtener el index del último punto
                // Si el dato tiene coma "," como separador de decimales
                if(index_coma >= index_punto){
                    dt = dt.replace(/\./g, ""); // quitar los puntos de dt
                    dt = dt.replace(",", "."); // reemplazar coma por punto
                } else {
                    dt = dt.replace(/\,/g, ""); // quitar las comas de dt
                }
                array_row.push(parseFloat(dt));
            } else {
                array_row.push(dt);
            }
        }

        data_final.push(array_row)
    }

    var num_cols = data_final[0].length; // Cantidad de columnas de la tabla HTML
    var num_rows = data_final.length; // Cantidad de filas de la tabla HTML
    var ws = XLSX.utils.aoa_to_sheet(data_final); // Genera la worksheet
    
    var range = { s: {r:0, c:0}, e: {r:num_rows, c:num_cols} }; // Objeto que simula rangos entre filas y columnas de la tabla HTML

    for(var R = range.s.r; R <= range.e.r; ++R) {
        for(var C = range.s.c; C <= range.e.c; ++C) {
            var cell = ws[XLSX.utils.encode_cell({r:R,c:C})];
            if(!cell || cell.t != 'n') continue; // solo formatear celdas numéricas
            if( cell && cell.t == 'n' && Number.isInteger(cell.v) && R == 0){
                var fmt = "0"; // El formato que tendrá el dato numérico en el archivo Excel
            } else {
                var fmt = "######0.0"; // El formato que tendrá el dato numérico en el archivo Excel
            }
            cell.z = fmt;
        }
    }

    // Genera el workbook
    var wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "sheet1");

    // Genera archivo y descarga
    var wbout = XLSX.write(wb, { type: "array", bookType: "xlsx" });
    var file = file_name + "." + file_type;
    saveAs(new Blob([wbout], { type: "application/octet-stream" }), file);

}

/*
function sheetjs_exportData(id_table, file_name, file_type = "xlsx"){
    var table = document.getElementById(id_table);
    var file = file_name + "." + file_type;
    var wb = XLSX.utils.table_to_book(table);
    XLSX.writeFile(wb, file);
}
*/
