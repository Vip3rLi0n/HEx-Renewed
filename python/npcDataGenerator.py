import mysql.connector
import json

db = mysql.connector.connect(host="localhost", port="6666", user="he", passwd="REDACTED", database="game")
cur = db.cursor()

with open('../json/npc.json') as json_file:
	npcList = json.load(json_file)

def match_slash(txt):
	subArray = None
	subgroup = txt.split('/')
	for id, key in enumerate(subgroup):

		if key == '<':
			#HTML bug fix (</span>)
			break
		if id == 0:
			subArray = None
			baseArray = npcList
		else:
			baseArray = subArray
		try:
			subArray = baseArray[key]
		except KeyError:
			pass
	return subArray
def getIP(key):
	cur.execute("""SELECT INET_NTOA(npc.npcIP)
				FROM npc_key 
				INNER JOIN npc
				ON npc.id = npc_key.npcID
				WHERE npc_key.key = %s
				LIMIT 1
			""", (key,))
	for ip in cur.fetchall():
		return ip[0]
	return 'Unknown IP'
def choose_language(info, language):

	if language == 'en':
		return info['en']
	else:
		try:
			return info[language]
		except KeyError:
			return info['en']

def getInfo(npcInfo, match, language='en'):
	if not npcInfo:
		return
	try:
		return choose_language(npcInfo, language)
	except KeyError:
		pass
	if match[-2::] == 'ip':
		return getIP(match[:-3:])
def web_format(txt, language):
	parted = txt.split('::')
	if len(parted) == 1:
		return txt;
	for match in parted:
		try:
			value = getInfo(npcList[match], match, language)
			continue
		except KeyError:
			value = getInfo(match_slash(match), match, language)
		print(value)
		if value:
			txt = txt.replace('::'+match+'::', value)
	return txt
def add(npcType, npcInfo, key):
    try:
        npcID = None  # Initialize npcID with a default value
        cur.execute(""" SELECT npcID
                        FROM npc_key
                        WHERE npc_key.key = %s
                        LIMIT 1
                    """, (key,))
        for npcID_row in cur.fetchall():
            npcID = str(npcID_row[0])

        if npcID is not None:  # Check if npcID has been assigned a value
            for language in npcInfo['name']:
                npcName = npcInfo['name'][language]
                npcWeb = web_format(npcInfo['web'][language], language)
                table = 'npc_info_' + language

                cur.execute(""" UPDATE """ + table + """
                                SET
                                    web = %s,
                                    name = %s
                                WHERE npcID = %s
                            """, (npcWeb.encode('utf-8').decode('cp1252'), npcName.encode('utf-8').decode('cp1252'), npcID))
            db.commit()
        else:
            print('No npcID found for key: ' + key)
    except Exception as e:
        print('Rolling back ' + key)
        db.rollback()
        print(e)


for npcType in npcList:
	try:
		npcList[npcType]['hardware']
		add(npcList[npcType]['type'], npcList[npcType], npcType)
		continue
	except KeyError:
		pass

	try:
		numType = npcList[npcType]['type']
		for key in npcList[npcType]:
			if key != 'type':
				add(numType, npcList[npcType][key], npcType+'/'+key)
		continue
	except KeyError:
		pass
	try:
		numType = npcList[npcType]['type']
		#WHOIS, BANK, NPC, PUZZLE
		for key in npcList[npcType]:
			if key != 'type':
				add(numType, npcList[npcType][key], npcType+'/'+key)
		continue
	except KeyError:
		pass
	#WHOIS_MEMBER, HIRER
	for level in npcList[npcType]:
		numType = npcList[npcType][level]['type']
		if numType != 61:
			for key in npcList[npcType][level]:
				if key != 'type':
					add(numType, npcList[npcType][level][key], npcType+'/'+level+'/'+key)
		continue
db.commit()