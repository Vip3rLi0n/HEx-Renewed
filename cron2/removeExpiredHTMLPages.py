import mysql.connector
import os
import time

start_time = time.time()

db = mysql.connector.connect(host="localhost", port="3306", user="root", passwd="root", database="hexc")

cur = db.cursor()

#Remove /html/profile/id.html pages

expireInterval = 3600 #Profiles updated in the last hour are not removed

cur.execute("""	SELECT userID
				FROM cache_profile 
				WHERE 
					TIMESTAMPDIFF(SECOND, expireDate, NOW()) > """+str(expireInterval)+"""
			""")

for userID in cur.fetchall():

	userID = userID[0]

	try: 
		os.remove('../profile/'+str(userID)+'.html')
	except:
		pass

	cur.execute("""	DELETE
					FROM cache_profile 
					WHERE 
						userID = """+str(userID)+"""
				""")

db.commit()

print(time.strftime("%d/%m/%y %H:%M:%S"),' - ',__file__,' - ',round(time.time() - start_time, 4), "s")
