# Creador de inserts SQL actividades
import json
import calendar 
import datetime

f = open("insert_monitores.sql", "w")


json_data = open("actividades.json","r").read() 
parsed_json = (json.loads(json_data))
for monitor in parsed_json["Monitores"]:
    nombre = monitor["Nombre"] + " " +  monitor["Apellido1"]
    print nombre
    id = monitor["Id"]
    insert = "INSERT INTO `monitores` (idmonitor, nombre) VALUES (" + str(id) + ", '" +	nombre+"');\n"
    f.write(insert.encode('utf8'))

f.close()

