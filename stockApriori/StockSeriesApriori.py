# Author: Ackerley Cheng
# file encoding: utf-8


# input: an event series
# output: association rules

import copy

import Tools
from CustomExceptions import (AlgorithmInitializationFailedException,
                              ParameterIllegalException)
from EventFactory import EventFactory
from LargeItemTable import LargeItemTable
from Rule import Rule


class StockSeriesApriori:

    # members

    # minimum support
    minSup = None
    # minimum confidence
    minConf = None
    # windth of sliding window
    wWin = None
    # max allowed time period in a large item set
    maxGap = None
    # show hint
    showMsg = None
    filterNoise = None

    # private
    _ready = False
    _transNum = None
    _scalarMinSup = None
    _msg = None

    # functions

    # cons.
    def __init__(self,
                 minSup, minConf, wWin, maxGap,
                 showMsg=False,
                 filterNoise=True):
        if maxGap > wWin:
            raise ParameterIllegalException(
                'maxGap should be equal or less than wWin')
        self.minSup = minSup
        self.minConf = minConf
        self.wWin = wWin
        self.maxGap = maxGap
        self.showMsg = showMsg
        self.filterNoise = filterNoise
        self._ready = True
        self._msg = ''

    # start analyzing to find association rules
    def analyze(self, eventSeries):
        if not self._ready:
            raise AlgorithmInitializationFailedException()
        subSeriesSet = EventFactory.subSeriesSet(eventSeries, self.wWin)
        if self.filterNoise:
            self._filterNoise(subSeriesSet)
        self._transNum = len(subSeriesSet)
        self._scalarMinSup = int(self._transNum * self.minSup)
        # msg
        msg = '\ninputed parameters:\n' + self.dumpPars() + '\n'
        msg += '\ninputed series:\n' + str(eventSeries) + '\n'
        self._manegeMsg(msg)
        largeItemTables = self._largeItemSets(subSeriesSet)
        # msg
        msg = '\nLarge Item Set Done.\n'
        msg += '\nLarge Item Tables:\n'
        for table in largeItemTables:
            msg += str(table) + '\n\n'
        self._manegeMsg(msg)
        associationRules = self._associationRules(largeItemTables)
        msg = '\nAssociation Rules Done.\n\n'
        msg += 'All Done.\n'
        self._manegeMsg(msg)
        return associationRules

    def dumpPars(self):
        out = 'minSup: ' + str(self.minSup) + '\t'
        out += 'minConf: ' + str(self.minConf) + '\t'
        out += 'wWin: ' + str(self.wWin) + '\t'
        out += 'maxGap: ' + str(self.maxGap) + '\n'
        out += 'showMsg: ' + str(self.showMsg) + '\t'
        out += 'filterNoise: ' + str(self.filterNoise) + '\n'
        out += '_ready: ' + str(self._ready) + '\t'
        out += '_transNum: ' + str(self._transNum) + '\t'
        out += '_scalarMinSup: ' + str(self._scalarMinSup)
        return out

    def getMessage(self):
        return self._msg

    # find large itemsets
    def _largeItemSets(self, subSeriesSet):
        LargeItemTables = []
        # build L1
        msg = '\nstart, level=1 ...\n'
        self._manegeMsg(msg)
        La = LargeItemTable(1)
        for subSeries in subSeriesSet:
            for event in subSeries:
                eventRow = [event]
                pos = Tools.listInListSet(eventRow, La.eventRows)
                if pos == -1:
                    La.add(eventRow, 1)
                else:
                    La.countList[pos] += 1
        La.reduce(self._scalarMinSup)
        if(len(La) <= 0):
            return LargeItemTables
        LargeItemTables.append(La)
        lvCount = 2
        Lb = LargeItemTable(2)
        # L2 to Ln until convergence
        while(True):
            msg = '\nstart, level=' + str(lvCount) + ' ...\n'
            self._manegeMsg(msg)
            # A: build Lb from La
            len_ = len(La.eventRows)
            for idx1 in range(len_ - 1):
                for idx2 in range(idx1 + 1, len_):
                    newRow = self._mergeTrans(
                        lvCount - 1, La.eventRows,
                        La.eventRows[idx1], La.eventRows[idx2])
                    if newRow is not None:
                        Lb.add(newRow, 0)
            # B: if Lb is empty, terminal condition is statisfied
            if(len(Lb) <= 0):
                return LargeItemTables
            LargeItemTables.append(Lb)
            # C: count Lb from subSeriesSet
            for eIdx, eventRow in enumerate(Lb.eventRows):
                count = 0
                for subSeries in subSeriesSet:
                    if Tools.isSubSet(eventRow, subSeries):
                        count += 1
                Lb.countList[eIdx] = count
            # D: remove eventRow of which count does not reach min support
            Lb.reduce(self._scalarMinSup)
            # E: prepare for next round
            La = Lb
            lvCount += 1
            Lb = LargeItemTable(lvCount)

    # discern association rules
    def _associationRules(self, LITables):
        associationRules = []
        # start from L2
        for LITable in LITables[1:]:
            for tIdx, trans in enumerate(LITable.eventRows):
                cTrans = LITable.countList[tIdx]
                for cutPoint in range(1, LITable.level):
                    candiRule = Rule()
                    for idx in range(cutPoint):
                        candiRule.IF.append(trans[idx])
                    for idx in range(cutPoint, LITable.level):
                        candiRule.THEN.append(trans[idx])
                    iftIdx = Tools.listInListSet(
                        candiRule.IF,
                        LITables[cutPoint - 1].eventRows)
                    cCondi = LITables[cutPoint - 1].countList[iftIdx]
                    candiRule.conf = round(cTrans / cCondi, 3)
                    if candiRule.conf > self.minConf:
                        candiRule.sup = round(
                            cTrans / self._transNum, 3)
                        rlen = LITable.level - cutPoint - 1
                        thtIdx = Tools.listInListSet(
                            candiRule.THEN,
                            LITables[rlen].eventRows)
                        cResult = LITables[rlen].countList[thtIdx]
                        candiRule.lift = round(
                            (cTrans * self._transNum) / (cCondi * cResult),
                            3)
                        associationRules.append(candiRule)
        return associationRules

    # make legal (n+1)-transaction out of 2 n-transactions
    # return None if it is impossible or illegal
    # modified from ref. - the first apriori algorithm:
    #   Fast Algorithms for Mining Asssociation Rules
    #   by Rakesh Agrawal and Ramakrishnan Srikant*
    def _mergeTrans(self, n, Ln, transA, transB):
        n -= 1
        for idx in range(n):
            if transA[idx] != transB[idx]:
                return None
        if transA[n].samePoint(transB[n]):
            return None
        if (transB[n].timePoint - transA[n].timePoint) >= self.maxGap:
            return None
        if transA[n] < transB[n]:
            # generate new candidate
            trans = copy.deepcopy(transA)
            trans.append(transB[n])
            # check all subsets are in Ln
            for idx in range(n + 1):
                subset = copy.deepcopy(trans)
                del subset[idx]
                if Tools.listInListSet(subset, Ln) == -1:
                    return None
            return trans
        return None

    # remove somthing like '[stockName].t1.same.0' to reduce noise
    def _filterNoise(self, eventSeriesSet):
        for eventSeries in eventSeriesSet:
            it = 0
            while(it < len(eventSeries)):
                if eventSeries[it].extent == 0:
                    del eventSeries[it]
                else:
                    it += 1

    # show message if _msg is True
    def _manegeMsg(self, msg):
        self._msg += msg
        if self.showMsg:
            print(msg, flush=True)
