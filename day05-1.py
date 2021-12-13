import re
split = re.compile('[^0-9]+')
lines = []
size = (0,0)
with open('day05.txt') as file:
    while line := file.readline().rstrip():
        lines.append(line := list(int(_) for _ in split.split(line)))
        size = (
            max(size[0], line[0], line[2]),
            max(size[1], line[1], line[3]),
        )
print(size)
diagram = [ [0] * (size[1]+1) for _ in range(size[0]+1)]
for x1,y1,x2,y2 in lines:
    if x1 == x2:
        for y in range(min(y1,y2),max(y1,y2)+1):
            diagram[x1][y] += 1
    elif y1 == y2:
        for x in range(min(x1,x2),max(x1,x2)+1):
            diagram[x][y1] += 1
    # else:
    #     print(f'diagonal', x1,y1,x2,y2)

print('point > 1', sum(len([_ for _ in line if _ > 1]) for line in diagram))

            
