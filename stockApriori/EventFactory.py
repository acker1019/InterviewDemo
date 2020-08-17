# Author: Ackerley Cheng
# file encoding: utf-8


import copy

from Event import Event


class EventFactory:

    # members

    # functions

    @staticmethod
    def subSeriesSet(eventSeries, timeGap):
        '''
        cut main series with a sliding window of which width is timeGap
        '''

        subSeriesSet = []
        ssNum = len(eventSeries) - timeGap + 1
        for i in range(ssNum):
            subSeries = copy.deepcopy(eventSeries[i:i + timeGap])
            if len(subSeries) > 0:
                EventFactory.alignEventSeries(subSeries)
                subSeriesSet.append(subSeries)
        return subSeriesSet

    # treat those series with same length but different start point as the same
    @staticmethod
    def alignEventSeries(eventSeries):
        fix = eventSeries[0].timePoint - 1
        if fix == 0:
            return
        for event in eventSeries:
            event.timePoint -= fix

    # transform saxSeries into EventSeries
    # if it is changeOnly Mode, events without changing are skipped
    @staticmethod
    def extractEvent(stockName, saxSeries, MODE_changeOnly=False):
        eventSeries = []
        _len = len(saxSeries)
        for i in range(1, _len):
            diff = saxSeries[i] - saxSeries[i - 1]
            if diff < 0:
                direction = 'down'
            elif diff > 0:
                direction = 'up'
            else:
                if MODE_changeOnly:
                    continue
                else:
                    direction = 'same'
            event = Event(stockName, i, direction, diff)
            eventSeries.append(event)
        return eventSeries
