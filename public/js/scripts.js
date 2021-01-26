function deleteFunction(id) {
    if (confirm("Está seguro que desea eliminar?")) {
        window.location.href="http://localhost/proyectoRobertinoPonzetti/public/index.php/eliminarPersona/"+id+"";
    } else {
        window.location.href="http://localhost/proyectoRobertinoPonzetti/public/index.php/listaPersonas";
    }
}