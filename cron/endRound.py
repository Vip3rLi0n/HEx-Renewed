import mysql.connector
import datetime
import time
start_time = time.time()


# MySQL connection configuration
config = {
    'user': 'he',
    'password': 'REDACTED',
    'host': 'localhost',
    "port": 6666,
    'database': 'game',
}

# Function to get the file extension based on softType
def get_extension(soft_type):
    extensions = {
        '1': '.crc',
        '2': '.hash',
        '3': '.scan',
        '4': '.fwl',
        '5': '.hdr',
        '6': '.skr',
        '7': '.av',
        '8': '.vspam',
        '9': '.vwarez',
        '10': '.vddos',
        '11': '.vcol',
        '12': '.vbrk',
        '13': '.exp',
        '14': '.exp',
        '15': '.nmap',
        '16': '.ana',
        '17': '.torrent',
        '18': '.exe',
        '19': '.exe',
        '20': '.vminer',
        '29': '.doom',
        '30': '.txt',
        '31': '',
        '50': '.nsa',
        '51': '.emp',
        '90': '.vdoom',
        '96': '.vminer',
        '97': '.vddos',
        '98': '.vwarez',
        '99': '.vspam'
    }
    return extensions.get(soft_type, '')


# Function to convert softVersion to dot version format
def dot_version(soft_version):
    length = len(soft_version)
    if length == 1:
        str_edit = '0' + soft_version
    elif length == 2:
        str_edit = [soft_version[i:i+1] for i in range(length)]
    elif length == 3:
        str_edit = [soft_version[i:i+2] for i in range(length)]
    elif length == 4:
        str_edit = [soft_version[i:i+3] for i in range(length)]
    elif length == 5:
        str_edit = [soft_version[i:i+4] for i in range(length)]
    elif length == 6:
        str_edit = [soft_version[i:i+5] for i in range(length)]
    elif length == 7:
        str_edit = [soft_version[i:i+6] for i in range(length)]
    elif length == 8:
        str_edit = [soft_version[i:i+7] for i in range(length)]
    else:
        raise ValueError("Error at finishRound.py!")
    
    str_return = f"{str_edit[0]}.{str_edit[1]}"
    return str_return


def Start():
    # Establish a connection to the database
    cnx = mysql.connector.connect(**config)
    cursor = cnx.cursor()

    # Get the current round
    cursor.execute("SELECT id FROM round ORDER BY id DESC LIMIT 1")
    cur_round = cursor.fetchone()[0]

    # Update round status and insert into round_stats
    cursor.execute(f"UPDATE round SET status = 2, endDate = NOW() WHERE id = {cur_round}")
    cursor.execute(f"INSERT INTO round_stats (id) VALUES ({cur_round + 1})")

    best = {
        'user': {},
        'soft': {},
        'clan': {},
        'ddos': {}
    }

    # Rank users
    cursor.execute("""
        SELECT
            uid, exp, timePlaying, hackCount, ddosCount, ipResets, moneyEarned, moneyTransfered, moneyHardware, moneyResearch,
            warezSent, spamSent, bitcoinSent, profileViews,
            TIMESTAMPDIFF(DAY, dateJoined, NOW()) AS age, clan.name, users.login,
            (SELECT COUNT(DISTINCT userID) AS total FROM software_research WHERE software_research.userID = users_stats.uid) AS researchCount
        FROM users_stats
        INNER JOIN users ON users.id = users_stats.uid
        LEFT JOIN clan_users ON clan_users.userID = users_stats.uid
        LEFT JOIN clan ON clan.clanID = clan_users.clanID
        ORDER BY exp DESC
    """)

    rank = 0

    for row in cursor.fetchall():
        rank += 1

        cursor.execute(f"""
            SELECT softName, softVersion, softType
            FROM software
            WHERE userID = '{row[0]}' AND isNPC = 0 AND softtype < 30
            ORDER BY softVersion DESC
            LIMIT 1
        """)
        soft_info = cursor.fetchall()

        if len(soft_info) == 1:
            soft_name = soft_info[0][0] + get_extension(soft_info[0][2])
            soft_version = dot_version(soft_info[0][1])
        else:
            soft_name = soft_version = ''

        # Check for duplicate rows and drop them if they exist
        cursor.execute("""
            DELETE FROM hist_users
            WHERE (user, reputation, age, clanName, timePlaying, hackCount, ddosCount, bitcoinSent, ipResets, moneyEarned,
                   moneyTransfered, moneyHardware, moneyResearch, bestSoft, bestSoftVersion, warezSent, spamSent, profileViews, researchCount)
            IN (
                SELECT user, reputation, age, clanName, timePlaying, hackCount, ddosCount, bitcoinSent, ipResets, moneyEarned,
                       moneyTransfered, moneyHardware, moneyResearch, bestSoft, bestSoftVersion, warezSent, spamSent, profileViews, researchCount
                FROM (
                    SELECT user, reputation, age, clanName, timePlaying, hackCount, ddosCount, bitcoinSent, ipResets, moneyEarned,
                           moneyTransfered, moneyHardware, moneyResearch, bestSoft, bestSoftVersion, warezSent, spamSent, profileViews, researchCount,
                           ROW_NUMBER() OVER (PARTITION BY user, reputation, age, clanName, timePlaying, hackCount, ddosCount, bitcoinSent, ipResets,
                                                moneyEarned, moneyTransfered, moneyHardware, moneyResearch, bestSoft, bestSoftVersion,
                                                warezSent, spamSent, profileViews, researchCount ORDER BY (SELECT NULL)) AS rn
                    FROM hist_users
                ) AS duplicates
                WHERE rn > 1
            )
        """)

        cursor.execute(f"""
            INSERT INTO hist_users (round, rank, id, userID, user, reputation, age, clanName, timePlaying, hackCount, ddosCount,
                                bitcoinSent, ipResets, moneyEarned, moneyTransfered, moneyHardware, moneyResearch,
                                bestSoft, bestSoftVersion, warezSent, spamSent, profileViews, researchCount)
            VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
        """, (cur_round, rank, '', row[0], row[7], row[1], row[14], row[13], row[2], row[3], row[4], row[11], row[5],
            row[6], row[8], row[9], row[10], soft_name, soft_version, row[10], row[12]))


        
        if rank <= 10:
            best['user'][rank] = row[0]
        
    # Rank clan
    cursor.execute("""
        SELECT clan.clanID, clan.name, clan.nick, clan.power, clan_stats.won, clan_stats.lost, clan_stats.pageClicks,
        (
            SELECT COUNT(*)
            FROM clan_users
            WHERE clan_users.clanID = clan.clanID
            GROUP BY clan_users.clanID
        ) AS members
        FROM clan
        INNER JOIN clan_stats ON clan_stats.cid = clan.clanID
        ORDER BY clan.power DESC
    """)
    
    rank = 0
    
    for row in cursor.fetchall():
        rank += 1
        
        if row[4] == 0 and row[5] == 0:
            rate = -1
        elif row[4] == 0 and row[5] > 0:
            rate = 0
        else:
            rate = (row[4] / (row[4] + row[5])) * 100
        
        cursor.execute(f"""
            INSERT INTO hist_clans (id, rank, cid, name, nick, reputation, round, won, lost, clicks, members)
            VALUES ('', {rank}, {row[0]}, '{row[1]}', '{row[2]}', {row[3]}, {cur_round}, {row[4]}, {row[5]}, {row[6]}, {row[7]})
        """)
        
        if rank <= 10:
            best['clan'][rank] = row[0]
    
    # Rank software
    cursor.execute("""
        SELECT softName, softType, softVersion, userID, users.login
        FROM software
        JOIN users ON users.id = software.userID
        WHERE isNPC = 0 AND softtype < 30 AND softType <> 19
        ORDER BY softVersion DESC, softSize ASC
    """)
    
    rank = 0
    
    for row in cursor.fetchall():
        rank += 1
        
        cursor.execute(f"""
            INSERT INTO hist_software (id, rank, softName, softType, softVersion, owner, ownerID, round)
            VALUES ('', {rank}, '{row[0]}', {row[1]}, '{dot_version(row[2])}', '{row[4]}', '{row[3]}', {cur_round})
        """)
        
        if rank <= 10:
            best['soft'][rank] = row[3]
    
    # Rank ddos
    cursor.execute("""
        SELECT ranking_ddos.rank, attID, vicID, power, servers, att.login AS attUser, vic.login AS vicUser
        FROM round_ddos
        LEFT JOIN users att ON att.id = round_ddos.attID
        LEFT JOIN users vic ON vic.id = round_ddos.vicID
        INNER JOIN ranking_ddos ON round_ddos.id = ranking_ddos.ddosID
        WHERE vicNPC = 0
        ORDER BY power DESC, servers DESC
    """)
    
    rank = 0
    
    for row in cursor.fetchall():
        rank += 1
        
        cursor.execute(f"""
            INSERT INTO hist_ddos (id, rank, round, attID, attUser, vicID, vicUser, power, servers)
            VALUES ('', {rank}, {cur_round}, {row[1]}, '{row[5]}', {row[2]}, '{row[6]}', {row[3]}, {row[4]})
        """)
        
        if rank <= 10:
            best['ddos'][rank] = row[1]
    
    # History clan war
    cursor.execute("""
        SELECT idWinner, idLoser, scoreWinner, scoreLoser, startDate, endDate, bounty
        FROM clan_war_history
        ORDER BY endDate ASC
    """)
    
    for row in cursor.fetchall():
        cursor.execute(f"""
            INSERT INTO hist_clans_war (id, idWinner, idLoser, scoreWinner, scoreLoser, startDate, endDate, bounty, round)
            VALUES ('', {row[0]}, {row[1]}, {row[2]}, {row[3]}, '{row[4]}', '{row[5]}', {row[6]}, {cur_round})
        """)
    
    # History missions
    cursor.execute("""
        SELECT type, missionEnd, prize, userID, completed, npc.id AS hirerID
        FROM missions_history
        INNER JOIN npc ON npc.npcIP = hirer
        ORDER BY missionEnd ASC
    """)
    
    for row in cursor.fetchall():
        cursor.execute(f"""
            INSERT INTO hist_missions (id, userID, type, hirerID, prize, missionEnd, completed, round)
            VALUES ('', {row[3]}, '{row[0]}', {row[5]}, {row[2]}, '{row[1]}', {row[4]}, {cur_round})
        """)
    
    # History doom
    cursor.execute("""
        SELECT creatorID, clanID, status
        FROM virus_doom
    """)
    
    doomerID = None
    
    for row in cursor.fetchall():
        if row[2] == 3:
            doomerID = row[0]
        
        cursor.execute(f"""
            INSERT INTO hist_doom (round, doomCreatorID, doomClanID, status)
            VALUES ({cur_round}, {row[0]}, {row[1]}, {row[2]})
        """)
    
    # MySQL dump
    output_file_path = f"../../database_dump_{datetime.datetime.now().strftime('%Y-%m-%d_%H-%M-%S')}.sql"
    with open(output_file_path, 'w') as file:
        for table in cursor.tables():
            table_name = table.table_name
            cursor.execute(f"SHOW CREATE TABLE {table_name}")
            create_table_stmt = cursor.fetchone()[1]
            file.write(f"{create_table_stmt};\n\n")
            
            cursor.execute(f"SELECT * FROM {table_name}")
            rows = cursor.fetchall()
            for row in rows:
                values = "', '".join(map(str, row))
                insert_stmt = f"INSERT INTO {table_name} VALUES ('{values}');\n"
                file.write(insert_stmt)
                
            file.write("\n")
            
    print(f"Database dumped successfully to: {output_file_path}")
    
    # Truncate tables
    cursor.execute("TRUNCATE TABLE bankAccounts")
    cursor.execute("TRUNCATE TABLE bankaccounts_expire")
    cursor.execute("TRUNCATE TABLE bitcoin_wallets")
    cursor.execute("UPDATE cache SET reputation = 0")

Start()
print(time.strftime("%d/%m/%y %H:%M:%S"),' - ',__file__,' - ',round(time.time() - start_time, 4), "s\n")