import sys

def add(total):
    queryToAdd = total
    print("adding " + str(total))
    with open('status/queries.txt', 'r+') as f:
        totalQuery = f.read()
        newTotal = int(totalQuery) + int(queryToAdd)
        f.seek(0)
        f.write(str(newTotal))
        f.truncate()

add(sys.argv[1])
