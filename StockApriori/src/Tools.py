# Author: Ackerley Cheng
# file encoding: utf-8

def sameList(listA, listB):
    return (len(listA) == len(listB)) and (set(listA) == set(listB))


def listInListSet(list, listSet):
    for idx, ls in enumerate(listSet):
        if sameList(list, ls):
            return idx
    return -1


# check if listA is a subset of listB
def isSubSet(listA, listB):
    for item in listA:
        if item not in listB:
            return False
    return True


# dump rule with beautified format
def beautifiedRuleSet(ruleSet):
    ruleSet = list(sorted(ruleSet, key=lambda rule: rule.lift, reverse=True))
    if len(ruleSet) <= 0:
        out = 'found nothing.'
    else:
        out = 'Association Rules:\n'
        out += '[support,\tconfidence,\tlift,\t\trule\n'
        for rule in ruleSet:
            out += '[' + str(rule.sup) + ',\t\t' + str(rule.conf)
            out += ',\t\t' + str(rule.lift) + ',\t\t'
            out += '{IF ' + str(rule.IF) + ' THEN ' + str(rule.THEN) + '} ]'
            out += '\n'
    return out
