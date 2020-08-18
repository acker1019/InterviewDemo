# Author: Ackerley Cheng
# file encoding: utf-8

import csv
import io
import os
import sys
from pathlib import Path

from src import Tools
from src.EventFactory import EventFactory
from src.StockSeriesApriori import StockSeriesApriori

# envir

doc_encoding = 'utf-8'
sys.stdout = io.TextIOWrapper(sys.stdout.detach(), encoding=doc_encoding)
sys.stderr = io.TextIOWrapper(sys.stderr.detach(), encoding=doc_encoding)

PJDIR = Path(os.path.dirname(__file__))

# script

outputDir = PJDIR / 'output/'

inputDir = PJDIR / 'data/stocksComSax/'
files = os.listdir(inputDir)

# preprocessing

eventSeries = []
# choose files in definded path from 1 to 5
count = 31
# instantiate apriori object
apriori = StockSeriesApriori(0.03, 0.6, 5 * count, 5, False)
# grasp data from directory: './stocksComSax/'
for file in files:
    with open(inputDir / file) as saxFile:
        saxSeries = list(csv.reader(saxFile))

    saxSeries = saxSeries[0]
    saxSeries = list(map(int, saxSeries))

    stockName = os.path.splitext(file)[0]
    eventSeries.extend(EventFactory.extractEvent(stockName, saxSeries, False))

    print('-- Stock Series: \'' + stockName + '\' loaded.')

    # stocks number constrainer
    if(count == 1):
        break
    else:
        count -= 1

# make series in order
eventSeries.sort()

print()
print('searching ...')

# start mining with apriori
assocRules = apriori.analyze(eventSeries)

# show result
outTxt = apriori.getMessage() + '\n' + Tools.beautifiedRuleSet(assocRules)
print(outTxt)

if not os.path.exists(outputDir):
    os.makedirs(outputDir)

with open(outputDir / 'result.txt', 'w') as rFile:
    rFile.write(outTxt)
