#!/bin/sh

/HEenv/Scripts/python ../cron2/updateCurStats.py; 
/HEenv/Scripts/python ../cron2/updateRanking.py; 
/HEenv/Scripts/python ../python/rank_generator.py;