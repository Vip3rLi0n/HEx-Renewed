import mysql.connector
import time

# Connect to the MySQL database
cnx = mysql.connector.connect(
    host='localhost',
    user='he',
    password='REDACTED',
    database='game',
    port='6666'
)

# Function to insert rows from npc table into the log table
def insert_npc_logs():
    # Get the list of npc IDs from the npc table
    download_center = "SELECT id FROM npc WHERE npcType = 8"
    cursor.execute(download_center)
    dc_ids = cursor.fetchall()
    npc_ids_query = "SELECT id FROM npc"
    cursor.execute(npc_ids_query)
    npc_ids = cursor.fetchall()

    # Iterate through each npc ID
    for npc_id in npc_ids:
        # Check if the npc ID already exists in the log table
        exists_query = f"SELECT COUNT(*) FROM log WHERE userID = {npc_id[0]} AND isNPC = 1"
        cursor.execute(exists_query)
        result = cursor.fetchone()

        if result[0] == 0 and npc_id not in dc_ids:
            # Insert a new row if the npc ID doesn't exist
            insert_query = f"INSERT INTO log (userID, isNPC) VALUES ({npc_id[0]}, 1)"
            cursor.execute(insert_query)

    # Commit the changes to the database
    cnx.commit()

# Function to insert rows from users table into the log table
def insert_user_logs():
    # Get the list of user IDs from the users table
    user_ids_query = "SELECT id FROM users"
    cursor.execute(user_ids_query)
    user_ids = cursor.fetchall()

    # Iterate through each user ID
    for user_id in user_ids:
        # Check if the user ID already exists in the log table
        exists_query = f"SELECT COUNT(*) FROM log WHERE userID = {user_id[0]} AND isNPC = 0"
        cursor.execute(exists_query)
        result = cursor.fetchone()

        if result[0] == 0:
            # Insert a new row if the user ID doesn't exist
            insert_query = f"INSERT INTO log (userID, isNPC) VALUES ({user_id[0]}, 0)"
            cursor.execute(insert_query)

    # Commit the changes to the database
    cnx.commit()

# Measure the script execution time
start_time = time.time()

try:
    # Create a cursor to execute SQL queries
    cursor = cnx.cursor()

    # Call the function to insert rows from the npc table into the log table
    insert_npc_logs()

    # Call the function to insert rows from the users table into the log table
    insert_user_logs()

    # Close the cursor
    cursor.close()

    # Print success message and total execution time
    print("Logs inserted successfully.")
except mysql.connector.Error as err:
    # Print error message if there's any database error
    print(f"Error inserting logs: {err}")
finally:
    # Close the database connection
    cnx.close()

# Print the total execution time
print("Total time:", round(time.time() - start_time, 4), "s")