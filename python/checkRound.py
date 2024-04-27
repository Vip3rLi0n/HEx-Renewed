import mysql.connector
import time
from datetime import datetime, timedelta

start_time = time.time()
db = mysql.connector.connect(host="localhost", port="3306", user="root", passwd="root", database="hexc")
cur = db.cursor()

# Execute the first query
cur.execute("SELECT id FROM round WHERE status = 0 LIMIT 1")
roundID = cur.fetchone()


# Check if the result is null or 0
if roundID is None or roundID[0] == 0:
    start_date = (datetime.now() + timedelta(days=1)).strftime('%Y-%m-%d %H:%M:%S')
    # Execute the second query
    cur.execute("INSERT INTO round (id, name, startDate) VALUES (1, %s, %s)", ("ROUND 1", start_date,))
    db.commit()
    cur.close()
    db.close()
else:
    print("Round exist. Skipping.")

# Print the total execution time
print("Total time:", round(time.time() - start_time, 4), "s")