f_in = open("carreras.txt", "r")
lineas = f_in.readlines() 
f_in.close()

f_out = open("insert_carreras.sql", "w")

count = 1
# Strips the newline character 
for linea in lineas: 
	insert = "INSERT INTO `carreras` (`idcarrera`, `nombre`) VALUES ("+str(count)+", '"+linea.strip()+"');\n"
	f_out.write(insert)
	print("Line {}: {}".format(count, linea.strip())) 
	count += 1

f_out.close()