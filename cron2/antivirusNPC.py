import mysql.connector
import time

start_time = time.time()

db = mysql.connector.connect(host="localhost", port="6666", user="he", passwd="REDACTED", database="game")
cur = db.cursor()

cur.execute("""
    SELECT npc_reset.npcID, npc.npcIP
    FROM npc_reset
    INNER JOIN npc ON npc.id = npc_reset.npcID
    WHERE TIMESTAMPDIFF(SECOND, NOW(), npc_reset.nextScan) < 0
""")

for npcID, npcIP in cur.fetchall():
    npcID = str(npcID)
    npcIP = str(npcIP)

    cur.execute("""
        SELECT software.id, software.softName, software.softVersion, software.softType
        FROM software
        INNER JOIN (
            SELECT virus.virusID
            FROM virus
            WHERE virus.installedip = %s
        ) v ON v.virusID = software.id
    """, (npcIP,))

    for virusID, softName, softVersion, softType in cur.fetchall():
        virusID = str(virusID)

        # Get userID who will be affected by the deletion
        cur.execute("""
            SELECT userID
            FROM lists
            WHERE virusID = %s
        """, (virusID,))

        for userID in cur.fetchall():
            userID = str(userID[0])
            virusName = softName

            if softType == 97:
                virusName += '.vddos'
            elif softType == 98:
                virusName += '.vwarez'
            elif softType == 99:
                virusName += '.vpsam'

            virusName += str(softVersion)

            # Add notifications to those users who will have the virus deleted.
            cur.execute("""
                INSERT INTO lists_notifications
                    (userID, ip, notificationType, virusName)
                VALUES
                    (%s, %s, '3', %s)
            """, (userID, npcIP, virusName))

        # Remove from the list
        cur.execute("""
            UPDATE lists
            SET
                virusID = 0
            WHERE ip = %s
        """, (npcIP,))

        # Delete the virus
        cur.execute("""
            DELETE FROM virus_ddos
            WHERE ip = %s
        """, (npcIP,))
        cur.execute("""
            DELETE FROM virus
            WHERE installedIp = %s
        """, (npcIP,))
        cur.execute("""
            DELETE FROM software_running
            WHERE userID = %s AND isNPC = '1'
        """, (npcID,))
        cur.execute("""
            DELETE FROM software
            WHERE userID = %s AND isNPC = '1'
        """, (npcID,))

scanInterval = '1' #av scan every 7 days.

cur.execute("""
    UPDATE npc_reset
    SET
        nextScan = DATE_ADD(NOW(), INTERVAL %s DAY)
    WHERE TIMESTAMPDIFF(SECOND, NOW(), npc_reset.nextScan) < 0
""", (scanInterval,))

db.commit()

print(time.strftime("%d/%m/%y %H:%M:%S"), ' - ', __file__, ' - ', round(time.time() - start_time, 4), "s\n")
