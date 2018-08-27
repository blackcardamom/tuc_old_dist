<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>

</body>

<script>
    function queryToObject(query) {
        var outObj = {};
        var expr = new RegExp('[^?&]+', 'g');
        while( match = expr.exec(query)) {
            var splitMatch = match[0].split("=");
            var key = splitMatch[0];
            var value = splitMatch[1];
            if (key.substring(key.length - 2) == "[]") {
                var newKey = key.slice(0,-2);
                if (!Array.isArray(outObj[newKey])) {
                    outObj[newKey] = [];
                }
                outObj[newKey].push(value);
            } else {
                outObj[key]=value;
            }
        }
        return outObj;
    }

    function objectToQuery(obj) {
        var outStr = "?";
        var first_loop = true;
        for(var key in obj) {
            if(Array.isArray(obj[key])) {
                for(var subkey in obj[key]) {
                    outStr += key+"[]="+obj[key][subkey]+"&";
                }
            } else {
                outStr += key+"="+obj[key]+"&";
            }
        }
        return outStr.slice(0,-1);
    }

    var currentQuery = window.location.search
    console.log(currentQuery);
    var queryObj = queryToObject(currentQuery);
    console.log(queryObj);
    console.log(objectToQuery(queryObj));
</script>

</html>
