/**
@namespace Converts JSON to CSV.

Compress with: http://jscompress.com/
*/
(function (window) {
    "use strict";
    /**
    Default constructor
    */
    var _CSV = function (JSONData, headers, options) {
        if (typeof JSONData === 'undefined')
            return;
        
        if (typeof options === 'undefined'){
            options = {};
        }

        var csvData = typeof JSONData != 'object' ? JSON.parse(settings.JSONData) : JSONData,
            csvHeaders,
            csvEncoding = 'data:text/csv;charset=utf-8,',
            csvOutput = "",
            csvRows = [],
            BREAK = '\r\n',
            DELIMITER = options.separator ? options.separator : ',',
			FILENAME = options.fileName ? options.fileName : "quiz_questions_export.csv";

        // Get and Write the headers
        csvHeaders = Object.keys(csvData[0]);
        csvOutput += headers.join(',') + BREAK;

        for (var i = 0; i < csvData.length; i++) {
            var rowElements = [];
            for(var k = 0; k < csvHeaders.length; k++) {
                rowElements.push(csvData[i][csvHeaders[k]]);
            } // Write the row array based on the headers
            csvRows.push(rowElements.join(DELIMITER));
        }

        csvOutput += csvRows.join(BREAK);

        // Initiate Download
        var a = document.createElement("a");

        if (navigator.msSaveBlob) { // IE10
            navigator.msSaveBlob(new Blob([csvOutput], { type: "text/csv" }), FILENAME);
        } else if ('download' in a) { //html5 A[download]
            a.href = csvEncoding + encodeURIComponent(csvOutput);
            a.download = FILENAME;
            document.body.appendChild(a);
            setTimeout(function() {
                a.click();
                document.body.removeChild(a);
            }, 66);
        } else if (document.execCommand) { // Other version of IE
            var oWin = window.open("about:blank", "_blank");
            oWin.document.write(csvOutput);
            oWin.document.close();
            oWin.document.execCommand('SaveAs', true, FILENAME);
            oWin.close();
        } else {
            alert("Support for your specific browser hasn't been created yet, please check back later.");
        }
    };

    window.CSVExport = _CSV;

})(window);

// From https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/keys
if (!Object.keys) {
  Object.keys = (function() {
    'use strict';
    var hasOwnProperty = Object.prototype.hasOwnProperty,
        hasDontEnumBug = !({ toString: null }).propertyIsEnumerable('toString'),
        dontEnums = [
          'toString',
          'toLocaleString',
          'valueOf',
          'hasOwnProperty',
          'isPrototypeOf',
          'propertyIsEnumerable',
          'constructor'
        ],
        dontEnumsLength = dontEnums.length;

    return function(obj) {
      if (typeof obj !== 'object' && (typeof obj !== 'function' || obj === null)) {
        throw new TypeError('Object.keys called on non-object');
      }

      var result = [], prop, i;

      for (prop in obj) {
        if (hasOwnProperty.call(obj, prop)) {
          result.push(prop);
        }
      }

      if (hasDontEnumBug) {
        for (i = 0; i < dontEnumsLength; i++) {
          if (hasOwnProperty.call(obj, dontEnums[i])) {
            result.push(dontEnums[i]);
          }
        }
      }
      return result;
    };
  }());
}

var emitXmlHeader = function (headers) {
    var headerRow =  '<ss:Row>\n';
    for (var colName in headers) {
        headerRow += '  <ss:Cell>\n';
        headerRow += '    <ss:Data ss:Type="String">';
        headerRow += colName + '</ss:Data>\n';
        headerRow += '  </ss:Cell>\n';        
    }
    headerRow += '</ss:Row>\n';    
    return '<?xml version="1.0"?>\n' +
           '<ss:Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"\n' + 
                'xmlns:o="urn:schemas-microsoft-com:office:office"\n' +
                'xmlns:x="urn:schemas-microsoft-com:office:excel"\n' +
                'xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"\n' +
                'xmlns:html="http://www.w3.org/TR/REC-html40">\n' +
           '<ss:Worksheet ss:Name="Quiz questions">\n' +
           '<ss:Table>\n\n' + headerRow;
};

var emitXmlFooter = function() {
    return '\n</ss:Table>\n' +
           '</ss:Worksheet>\n' +
           '</ss:Workbook>\n';
};

var jsonToSsXml = function (jsonObject, headers) {
    var row;
    var col;
    var xml;
    var data = typeof jsonObject != "object" ? JSON.parse(jsonObject) : jsonObject;
    
    xml = emitXmlHeader(headers);

    for (row = 0; row < data.length; row++) {
        xml += '<ss:Row>\n';
      
        for (col in data[row]) {
            xml += '<ss:Cell>\n';
            xml += '<ss:Data ss:Type="' + headers[col]  + '">';
            xml += data[row][col] + '</ss:Data>\n';
            xml += '</ss:Cell>\n';
        }

        xml += '</ss:Row>\n';
    }
    
    xml += emitXmlFooter();
    return xml;  
};

xlsExporter = function (content, filename, contentType) {
    var contentMimeType;
    
    if (typeof contentType === 'undefined'){
        contentType = 'xls';
    }
    switch(contentType){
        case 'xls':
           contentMimeType = 'application/vnd.ms-excel';
        break;
        case 'xlsx':
           contentMimeType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        break;
        default:
           contentMimeType = 'application/vnd.ms-excel';
        break;
    }    
    
    var a = document.getElementById('downloadFile');
    var blob = new Blob([content], {
        'type': contentMimeType
    });
    a.href = window.URL.createObjectURL(blob);
    a.download = filename + '.' + contentType;
    a.click();
    window.URL.revokeObjectURL(a.href);
};
