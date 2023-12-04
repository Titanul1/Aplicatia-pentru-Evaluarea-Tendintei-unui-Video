import sys
import pandas as pd
import os 
from typing import List, Tuple, Union
import seaborn as sns
import numpy as np 
import matplotlib.pyplot as plt 
from collections import Counter
from string import punctuation
import re
import math

from wordcloud import WordCloud

import nltk
from nltk.sentiment.vader import SentimentIntensityAnalyzer
from nltk.corpus import stopwords
from nltk.stem import WordNetLemmatizer
from nltk.corpus import wordnet
from nltk.tokenize import word_tokenize

from flair.models import TextClassifier
from flair.data import Sentence

from scipy import stats 
from sentence_transformers import SentenceTransformer, util

sys.stdout.reconfigure(encoding='utf-8')
comentariuCSV = sys.argv[1]
cuvstring = sys.argv[2]
#comentariuCSV = "comments_FxJ3zPUU6Y4.csv" #"comments.csv" #
#cuvstring =  "happy, asdfakj, lofi"#"woman, trump"#
cuvector = re.split(r',\s*', cuvstring) #regex pentru a elimina spatii, pune cuvinte chei in vector
df = pd.read_csv(comentariuCSV)
df.head()
sid = SentimentIntensityAnalyzer()
df['Sentiment'] = df['Comment'].apply(lambda x: sid.polarity_scores(x)['compound'])
scores = df['Sentiment']
#print(df[['Author', 'Comment', 'Sentiment']])
plt.figure(figsize=(15, 5))
plt.hist(scores)
plt.xlim(-1, 1)
pngfile = comentariuCSV[8:-4] #comentariuCSV[:-4]#
plt.savefig(pngfile+".png")
#plt.show()
plt.close()
print(pngfile+".png")
meanformat = '{:.2f}'.format(np.mean(scores))
print(meanformat)
stdformat = '{:.2f}'.format(np.std(scores))
print(stdformat)
for cuvant in cuvector:
    print(cuvant)
    dfiltrat = df.loc[df['Comment'].str.contains(cuvant, case=False)]
    if dfiltrat.empty:
        print("empty")
    else:
        cuvpngfile = pngfile+"_"+cuvant+".png"
        print(cuvpngfile)
        stdfiltrat = dfiltrat['Sentiment'].std()
        mediefiltrat = dfiltrat['Sentiment'].mean()
        if math.isnan(stdfiltrat):
            print("n")
        else:
            print('{:.2f}'.format(stdfiltrat))
        print('{:.2f}'.format(mediefiltrat))
        minid = dfiltrat['Sentiment'].idxmin()
        min = dfiltrat['Sentiment'].min()
        celmaineg = dfiltrat.at[minid, 'Comment']
        print('{:.2f}'.format(min))
        print(celmaineg)
        maxid = dfiltrat['Sentiment'].idxmax()
        max = dfiltrat['Sentiment'].max()
        celmaipoz = dfiltrat.at[maxid, 'Comment']
        print('{:.2f}'.format(max))
        print(celmaipoz)
        cuvantscores = dfiltrat['Sentiment']
        plt.figure(figsize=(15, 5))
        plt.hist(cuvantscores)
        plt.title("Distributia Sentimentelor in Comentarii cu Mentiune de \'" + cuvant + "\'")
        plt.xlim(-1, 1)
        plt.savefig(cuvpngfile)
        #plt.show()
        plt.close()