import sys
import mysql.connector
import string
import random
import os
import bcrypt
import time
start_time = time.time()

user = sys.argv[1]
plaintextPass = sys.argv[2]
email = sys.argv[3]
gameIP = ".".join(str(random.randint(0, 255)) for _ in range(4))  # Generate random IP address
externalID = sys.argv[4]

if int(externalID) > 0:
    social_network = sys.argv[5]

def pwd_generator(size=8, chars=string.ascii_uppercase + string.digits + string.ascii_lowercase):
    return ''.join(random.choice(chars) for _ in range(size))

cur = False

try:
    db = mysql.connector.connect(host="localhost", port="6666", user="he", passwd="REDACTED", database="game")
    cur = db.cursor()

    hashedPass = bcrypt.hashpw(plaintextPass.encode(), bcrypt.gensalt())

    cur.execute("INSERT INTO users (login, password, gamePass, email, gameIP) VALUES (%s, %s, %s, %s, INET_ATON(%s))",
                (user, hashedPass, pwd_generator(), email, gameIP))
    last_inserted_id = cur.lastrowid
    userID = str(last_inserted_id)

    cur.execute("INSERT INTO users_stats (uid, dateJoined) VALUES ("+userID+", NOW())")
    cur.execute("INSERT INTO hardware (userID, name) VALUES ("+userID+", 'Server #1')")
    cur.execute("INSERT INTO log (userID, text) VALUES ("+userID+", CONCAT(SUBSTRING(NOW(), 1, 16), ' - localhost installed current operating system'))")
    cur.execute("INSERT INTO cache (userID) VALUES ("+userID+")")
    cur.execute("INSERT INTO cache_profile (userID, expireDate) VALUES ("+userID+", NOW())")
    cur.execute("INSERT INTO hist_users_current (userID) VALUES ("+userID+")")
    cur.execute("INSERT INTO ranking_user (userID, rank) VALUES ("+userID+", '-1')")
    cur.execute("INSERT INTO certifications (userID) VALUES ("+userID+")")
    cur.execute("INSERT INTO users_puzzle (userID) VALUES ("+userID+")")
    cur.execute("INSERT INTO users_learning (userID) VALUES ("+userID+")")
    cur.execute("INSERT INTO users_language (userID) VALUES ("+userID+")")

    if int(externalID) > 0:
        if social_network == 'facebook':
            cur.execute("INSERT INTO users_facebook (externalID, gameID) VALUES ("+externalID+", "+user+")")
        elif social_network == 'twitter':
            cur.execute("INSERT INTO users_twitter (externalID, gameID) VALUES ("+externalID+", "+user+")")

    db.commit()

except Exception as e:
    if cur:
        print("Error: Rolling back create_user "+user+" using "+email)
        print(e)
        db.rollback()

finally:
    if cur:
        db.close()
        os.system('python ../python/profile_generator.py '+str(userID)+' en')

print(time.strftime("%d/%m/%y %H:%M:%S"),' - ',__file__,' - ',round(time.time() - start_time, 4), "s\n")