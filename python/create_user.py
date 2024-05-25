import sys
import mysql.connector
import string
import random
import os
import bcrypt
import time
import ipaddress

start_time = time.time()

user = sys.argv[1]
plaintextPass = sys.argv[2]
email = sys.argv[3]
gameIP = ".".join(str(random.randint(0, 255)) for _ in range(4))  # Generate random IP address
realIP = ipaddress.ip_address(sys.argv[4])
homeIP = ipaddress.ip_address(sys.argv[5])
social_network = int(sys.argv[6])

def pwd_generator(size=8, chars=string.ascii_uppercase + string.digits + string.ascii_lowercase):
    return ''.join(random.choice(chars) for _ in range(size))

db = mysql.connector.connect(host="localhost", port="3306", user="root", passwd="root", database="hexc")
cur = db.cursor()

hashedPass = bcrypt.hashpw(plaintextPass.encode(), bcrypt.gensalt())

# Using prepared statements with parameterized queries
cur.execute("INSERT INTO users (login, password, gamePass, email, gameIP, realIP, homeIP) VALUES (%s, %s, %s, %s, INET_ATON(%s), %s, %s)",
            (user, hashedPass, pwd_generator(), email, str(gameIP), int(realIP), int(homeIP)))

last_inserted_id = cur.lastrowid
userID = str(last_inserted_id)

characters = string.ascii_letters + string.digits
bankNum = ''.join(str(random.randint(0, 9)) for _ in range(6))
bankPass = ''.join(random.choice(characters) for _ in range(6))
cur.execute("SELECT npcID FROM npc_info_en WHERE name = 'First International Bank' LIMIT 1")
bankFetchID = cur.fetchone()
bankID = bankFetchID[0]
cur.execute("INSERT INTO users_stats (uid, dateJoined) VALUES (%s, NOW())", (userID,))
cur.execute("INSERT INTO hardware (userID, name) VALUES (%s, 'Server #1')", (userID,))
cur.execute("INSERT INTO bankAccounts (bankAcc, bankPass, bankID, bankUser, cash, dateCreated) VALUES (%s, %s, %s, %s, '1000', NOW())", (bankNum, bankPass, bankID, userID))
cur.execute("INSERT INTO log (userID, text) VALUES (%s, CONCAT(SUBSTRING(NOW(), 1, 16), ' - localhost installed current operating system'))", (userID,))
cur.execute("INSERT INTO cache (userID) VALUES (%s)", (userID,))
cur.execute("INSERT INTO cache_profile (userID, expireDate) VALUES (%s, NOW())", (userID,))
cur.execute("INSERT INTO hist_users_current (userID, user) VALUES (%s, %s)", (userID, user))
cur.execute("INSERT INTO ranking_user (userID, rank) VALUES (%s, '-1')", (userID,))
cur.execute("INSERT INTO certifications (userID) VALUES (%s)", (userID,))
cur.execute("INSERT INTO users_puzzle (userID) VALUES (%s)", (userID,))
cur.execute("INSERT INTO users_learning (userID) VALUES (%s)", (userID,))
cur.execute("INSERT INTO users_language (userID) VALUES (%s)", (userID,))

# Fix this later to get twitter/fb id from user.
externalID = None
if social_network == 1:
    cur.execute("INSERT INTO users_facebook (externalID, gameID) VALUES (%s, %s)", (externalID, user))
elif social_network == 2:
    cur.execute("INSERT INTO users_twitter (externalID, gameID) VALUES (%s, %s)", (externalID, user))

db.commit()
db.close()

# Run profile generator
os.system(f'python ./profile_generator.py {userID}')

print(time.strftime("%d/%m/%y %H:%M:%S"), ' - ', __file__, ' - ', round(time.time() - start_time, 4), "s\n")
