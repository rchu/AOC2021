from copy import deepcopy
with open('day04.txt') as file:
    numbers = [int(_) for _ in file.readline().rstrip().split(',')]

    cards = []
    marked = []
    while file.readline(): # read blank lime
        card={}
        for y in range(5):
            for x,i in enumerate(file.readline().rstrip().split()):
                card[int(i)] = (x,y)
        cards.append(card)
        marked.append([ [0] * 5 , [0] * 5])

for i,number in enumerate(numbers):
    for card, mark in zip(cards, marked):
        if pos := card.pop(number,None):
            for xy in [0,1]:
                if mark[xy][pos[xy]] == 4:
                    print(f'BINGO {i=} {number=} {y=}\n{card=}\n{mark=}' )
                    
                    print(f'{number=} {sum(card.keys())=} result={number * sum(card.keys())}')

                    exit(0)
                else:
                    mark[xy][pos[xy]] += 1

