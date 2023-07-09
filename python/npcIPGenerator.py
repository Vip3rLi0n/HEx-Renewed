import json
import random
import mysql.connector
from ipaddress import IPv4Address
import os
import time
start_time = time.time()

def add_npc(npc_type, npc_info, key):
    try:
        try:
            npc_ip = npc_info['ip']
        except KeyError:
            npc_ip = str(IPv4Address(random.randint(0, 2**32 - 1)))

        # Insert into npc table
        cursor.execute(
            """INSERT INTO npc (npcType, npcIP, npcPass)
               VALUES (%s, INET_ATON(%s), %s)""",
            (npc_type, npc_ip, rand_string(8))
        )
        npc_id = str(cursor.lastrowid)

        # Insert into npc_info_lang tables
        for language in npc_info['name']:
            npc_name = npc_info['name'][language]
            npc_web = npc_info['web'][language]
            table = 'npc_info_' + language

            cursor.execute(
                f"""INSERT INTO {table} (npcID, name, web)
                    VALUES (%s, %s, %s)""",
                (npc_id, npc_name, npc_web)
            )

        # Insert into npc_key table
        cursor.execute(
            """INSERT INTO npc_key (npcID, `key`)
               VALUES (%s, %s)""",
            (npc_id, key)
        )

        # Insert into hardware table
        hardware = npc_info['hardware']
        cpu = hardware['cpu']
        hdd = hardware['hdd']
        ram = hardware['ram']
        net = hardware['net']

        cursor.execute(
            """INSERT INTO hardware (userID, name, cpu, hdd, ram, net, isNPC)
               VALUES (%s, '', %s, %s, %s, %s, '1')""",
            (npc_id, cpu, hdd, ram, net)
        )

        # Insert into log table
        cursor.execute(
            """INSERT INTO log (userID, isNPC)
               VALUES (%s, 1)""",
            (npc_id,)
        )

        next_scan = random.randint(1, 50)
        cursor.execute(
            """INSERT INTO npc_reset (npcID, nextScan)
               VALUES (%s, DATE_ADD(NOW(), INTERVAL %s HOUR))""",
            (npc_id, next_scan)
        )

        db.commit()
        print('Successfully added NPC.')

    except Exception as e:
        print('Exception occurred! Rolling back.')
        print(e)
        db.rollback()

def generate_npcs(npc_list):
    for npc_type in npc_list:
        try:
            npc_info = npc_list[npc_type]
            if 'hardware' in npc_info:
                add_npc(npc_info['type'], npc_info, npc_type)
                continue

            num_type = npc_info.get('type')
            if num_type is not None:
                for key in npc_info:
                    if key != 'type':
                        add_npc(num_type, npc_info[key], npc_type + '/' + key)

        except KeyError:
            pass

        if isinstance(npc_info, dict):
            for level in npc_info:
                level_info = npc_info[level]
                if isinstance(level_info, dict) and 'type' in level_info:
                    num_type = level_info['type']

                    if num_type != 61:
                        for key in level_info:
                            if key != 'type':
                                add_npc(num_type, level_info[key], npc_type + '/' + level + '/' + key)


# Load the NPC data from JSON file
with open('../json/npc.json') as file:
    npc_data = json.load(file)

# Connect to the MySQL database
db = mysql.connector.connect(host="localhost", port="6666", user="he", passwd="REDACTED", database="game")

# Create a cursor object
cursor = db.cursor()

# Define the rand_string function
def rand_string(length, charset='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'):
    return ''.join(random.choice(charset) for _ in range(length))

# Generate NPCs
generate_npcs(npc_data)

# Close the cursor and database connection
cursor.close()
db.close()

os.system('python ../python/software_generator.py')
os.system('python ../python/software_generator_riddle.py')
os.system('python ../python/npcDataGenerator.py')
print(time.strftime("%d/%m/%y %H:%M:%S"),' - ',__file__,' - ',round(time.time() - start_time, 4), "s\n")