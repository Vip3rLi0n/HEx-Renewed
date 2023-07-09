import mysql.connector
import time

# Ask for user confirmation
confirmation = input("Please be reminded that this script will clean the database and remove all data! Only proceed if you understand this! [Y/N]: ")

if confirmation.lower() != 'y':
    print("Script execution canceled.")
else:
    start_time = time.time()

    # Connect to MySQL
    cnx = mysql.connector.connect(
        host='localhost',
        port=6666,
        user='he',
        password='REDACTED',
        database='game'
    )

    # Create a cursor object
    cursor = cnx.cursor()

    # Disable foreign key checks
    cursor.execute('SET FOREIGN_KEY_CHECKS = 0;')

    # Drop the stored procedure if it exists
    cursor.execute('DROP PROCEDURE IF EXISTS delete_rows_except_changelog')

    # Create the stored procedure
    procedure_query = '''
    CREATE PROCEDURE delete_rows_except_changelog()
    BEGIN
      DECLARE done INT DEFAULT FALSE;
      DECLARE tableName VARCHAR(255);

      -- Cursor to iterate over table names
      DECLARE cur CURSOR FOR
        SELECT table_name
        FROM information_schema.tables
        WHERE table_schema = 'game'
          AND table_name != 'changelog';

      -- Error handler for cursor
      DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

      -- Iterate over table names and delete rows
      OPEN cur;
      read_loop: LOOP
        FETCH cur INTO tableName;
        IF done THEN
          LEAVE read_loop;
        END IF;

        SET @deleteQuery = CONCAT('DELETE FROM ', tableName, ';');
        PREPARE stmt FROM @deleteQuery;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
      END LOOP;

      CLOSE cur;
    END
    '''
    cursor.execute(procedure_query)

    # Call the stored procedure to delete rows
    cursor.callproc('delete_rows_except_changelog')

    # Enable foreign key checks
    cursor.execute('SET FOREIGN_KEY_CHECKS = 1;')

    # Commit the changes and close the cursor and connection
    cnx.commit()
    cursor.close()
    cnx.close()

    print("Successfully cleaned database!")
    print(time.strftime("%d/%m/%y %H:%M:%S"), ' - ', __file__, ' - ', round(time.time() - start_time, 4), "s\n")
