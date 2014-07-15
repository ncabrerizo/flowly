function validateNIF(nif) {

    if (nif.length != 9)
        return false;

    var validateDNI = function(dni) {

        var loockup = 'TRWAGMYFPDXBNJZSQVHLCKE';
        var valueDni = dni.substr(0, dni.length - 1);
        var letra = dni.substr(dni.length - 1, 1).toUpperCase();

        if (loockup.charAt(valueDni % 23) == letra)
            return true;

        return false;

    }

    var valueNif = nif.substr(1, nif.length - 2);
    var suma = 0;

    for (i = 1; i < valueNif.length; i = i + 2) {

        suma = suma + parseInt(valueNif.substr(i, 1));

    }

    var suma2 = 0;

    for (i = 0; i < valueNif.length; i = i + 2) {

        result = parseInt(valueNif.substr(i, 1)) * 2;

        if (String(result).length == 1) {

            suma2 = suma2 + parseInt(result);

        } else {

            suma2 = suma2 + parseInt(String(result).substr(0, 1)) + parseInt(String(result).substr(1, 1));

        }

    }

    suma = suma + suma2;
    var unidad = String(suma).substr(1, 1)
    unidad = 10 - parseInt(unidad);
    var primerCaracter = nif.substr(0, 1).toUpperCase();

    if (primerCaracter.match(/^[NPQRSVW]$/)) {

        if (String.fromCharCode(64 + unidad).toUpperCase() == nif.substr(nif.length - 1, 1).toUpperCase())
            return true;

    } else if (primerCaracter.match(/^[XYZ]$/)) {

        var newnif;
        if (primerCaracter == "X") newnif = nif.substr(1);
        else if (primerCaracter == "Y") newnif = "1" + nif.substr(1);
        else if (primerCaracter == "Z") newnif = "2" + nif.substr(1);
        return validateDNI(newnif);

    } else if (primerCaracter.match(/^[ABCDEFGHJU]$/)) {

        if (unidad == 10) unidad = 0;
        if (nif.substr(nif.length - 1, 1) == String(unidad)) return true;

    } else {

        return validateDNI(nif);

    }

    return false;

}