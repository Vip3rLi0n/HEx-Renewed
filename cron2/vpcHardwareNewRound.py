import mysql.connector
import time

start_time = time.time()

db = mysql.connector.connect(host="localhost", port="3306", user="root", passwd="root", database="hexc")
cur = db.cursor()

def insert_hardware():
    # Grab all IDs from the 'users' table
    cur.execute("SELECT id FROM users")
    user_ids = cur.fetchall()

    for user_id in user_ids:
        user_id = user_id[0]  # Extract the ID from the tuple

        # Check if the user ID already exists in the 'hardware' table
        cur.execute(f"SELECT COUNT(*) FROM hardware WHERE userID = {user_id}")
        count = cur.fetchone()[0]

        if count == 0:
            # Insert hardware record if the user ID doesn't exist
            cur.execute(f"INSERT INTO hardware (userID, name) VALUES ({user_id}, 'Server #1')")
        else:
            print(f"Hardware record for user ID {user_id} already exists.")

    db.commit()
    print("Hardware records inserted successfully.")

insert_hardware()

cur.close()
db.close()

print(time.strftime("%d/%m/%y %H:%M:%S"), ' - ', __file__, ' - ', round(time.time() - start_time, 4), "s\n")
