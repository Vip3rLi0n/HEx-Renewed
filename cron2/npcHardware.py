import mysql.connector
import time

start_time = time.time()

db = mysql.connector.connect(host="localhost", port="3306", user="root", passwd="root", database="hexc")
cur = db.cursor()

cur.execute("""SELECT
                   id
               FROM npc
               INNER JOIN npc_key ON npc_key.npcID = npc.id
            """)
rank = 0
for npcID in cur.fetchall():
    cur.execute("""UPDATE hardware
                   SET 
                       hdd = 50000,
                       cpu = 8000,
                       net = 500,
                       ram = 8192
                   WHERE 
                       isNPC = 1 AND
                       userID = %s
                """, (npcID[0],))

db.commit()

print(time.strftime("%d/%m/%y %H:%M:%S"), ' - ', __file__, ' - ', round(time.time() - start_time, 4), "s\n")