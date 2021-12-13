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
    for j, card, mark in zip(range(len(cards)), cards, marked):
        if (pos := card.pop(number,None)):
            for xy in [0,1]:
                mark[xy][pos[xy]] += 1
                if mark[xy][pos[xy]] == 5:
                    print(f'BINGO number #{i} = {number}\n      card #{j} = {card} {mark=}' )
                    print(f'{number=} {sum(card.keys())=} result={number * sum(card.keys())}')
                    
                    cards.pop(j)
                    marked.pop(j)

                    break

