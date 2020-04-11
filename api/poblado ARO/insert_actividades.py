# Creador de inserts SQL actividades
import json
import calendar 
import datetime

def diaSemana(fecha): 
    fecha = datetime.datetime.strptime(fecha, '%Y-%m-%d').weekday() 
    diasemanaIngles = calendar.day_name[fecha]
    if(diasemanaIngles == "Monday"):
    	return 'L'
    elif(diasemanaIngles == "Tuesday"):
    	return 'M'
    elif(diasemanaIngles == "Wednesday"):
    	return 'X'
    elif(diasemanaIngles == "Thursday"):
    	return 'J'
    elif(diasemanaIngles == "Friday"):
    	return 'V'


f = open("insert_actividades.sql", "w")


json_data = open("actividades.json","r").read() 
parsed_json = (json.loads(json_data))
for disponibilidad in parsed_json["Disponibilidad"]:
    for actividad in parsed_json["Actividades"]:
        if(actividad["Id"] == disponibilidad["IdActividad"]):
            nombre = actividad["Nombre"]
    insert = "INSERT INTO actividades (idactividad, hora, horaFin, diasemana, nombre, idmonitor) VALUES ("+str(disponibilidad['IdActividad'])+", '"+disponibilidad["HoraInicio"]+"','"+disponibilidad["HoraFin"]+"', '"+diaSemana(disponibilidad["Fecha"])+"', '"+	nombre +"', "+str(disponibilidad["IdMonitor"])+");\n"
    print insert
    f.write(insert.encode('utf8'))

f.close()

