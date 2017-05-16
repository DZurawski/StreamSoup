""" === BeautifulSoup General Scraping and File Functions ===
    @author Daniel Zurawski
"""

import requests
import bs4
import csv

def brewsoup(url):
    """ Create a soup object from an internet url.
        @param url -- A valid internet url.
        @return soup or None if HTTPError occurs.
    """
    try:
        res = requests.get(url)
        res.raise_for_status()
    except requests.exceptions.HTTPError as error:
        print(error)
        return None
    return bs4.BeautifulSoup(res.text, "html.parser")

def makeCSV(showlist, filename):
    """ Write a CSV file containing the information stored in showlist.
        @param showlist -- [[String]] a show's information
        @param filename -- String -- The name of the file to be created.
        @return Nothing
    """
    with open(filename, "w", encoding="utf-8") as file:
        csv.writer(file).writerows(showlist)