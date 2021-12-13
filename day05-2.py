import re
split = re.compile('[^0-9]+')
lines = []
size = (0,0)
with open('day05.txt') as file:
    while line := file.readline().rstrip():
        # print(line,end=' >>> ')
        lines.append(line := list(int(_) for _ in split.split(line)))
        print(line)
        size = (
            max(size[0], line[0], line[2]),
            max(size[1], line[1], line[3]),
        )
print(f'{size=}')
diagram = [ [0] * (size[1]+1) for _ in range(size[0]+1)]
for x1,y1,x2,y2 in lines:
    if x1 == x2:
        for y in range(min(y1,y2),max(y1,y2)+1):
            diagram[x1][y] += 1
    elif y1 == y2:
        for x in range(min(x1,x2),max(x1,x2)+1):
            diagram[x][y1] += 1
    else:
        dx = 1 if x1 < x2 else -1
        dy = 1 if y1 < y2 else -1
        for x,y in zip(range(x1,x2+dx,dx), range(y1,y2+dy,dy)):
            diagram[x][y] += 1


def map_graph(x):
    if x==0:
        return ' '
    elif x==1:
        return '*'
    else:
        return '█'

# for line in diagram:
#     print(f'{len([_ for _ in line if _ > 1]):4} |', ' '.join(map_graph(_) for _ in line))
            
print('point > 1 = ', sum(len([_ for _ in line if _ > 1]) for line in diagram))
