jquery(document).ready(function() {


    jquery.ajax({
    
       type: 'GET',
       url: 'get-csv.php',
       data: null,
       success: function(text) {
       
       
           var fields = text.split(/\n/);
           fields.pop(fields.length-1);
           
           
           var headers = fields[0].split(','), 
               html = '<table>';
           
           html += '<tr>';
           
           for(var i = 0; i < headers.length; i += 1) {
           
              html += '<th scope="col">' + headers[i] + '</th>';
              
           }
           
           html += '</tr>';
           
           var data = fields.slice(1, fields.length);
           
           
           
           for(var j = 0; j < data.length; j += 1) {
           
           
           
              var dataFields = data[j].split(',');
              
              html += '<tr>';
              html += '<td>' + dataFields[0] + '</td>';
              html += '<td><a href="' + dataFields[1] + '">' + dataFields[1] + '</a></td>';
              html += '<td>' + dataFields[2] + '</td>';
              html += '</tr>';
              
           
           
           }
           
           html += '</table>';
           
           
           $(html).appendTo('body');
           
           
           
       
       
       
       }
    
    
    
    });



});