
class Rule:

    # members

    IF = None
    THEN = None
    sup = None
    conf = None
    lift = None

    # functions

    # cons.
    def __init__(self):
        self.IF = []
        self.THEN = []

    # Override: toString
    def __repr__(self):
        out = '[' + str(self.sup) + ', ' + str(self.conf)
        out += ', ' + str(self.lift) + ', '
        out += '{IF ' + str(self.IF) + ' THEN ' + str(self.THEN) + '} ]'
        return out
