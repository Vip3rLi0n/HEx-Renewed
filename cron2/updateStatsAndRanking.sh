#!/bin/sh

python ../cron2/updateCurStats.py; 
python ../cron2/updateRanking.py; 
python ../python/rank_generator.py;