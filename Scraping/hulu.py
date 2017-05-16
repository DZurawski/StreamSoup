""" === Scraping TV Shows from Hulu ===
    @author Daniel Zurawski
"""

import ast
import scraping

def showlist(limiter=None):
    """ Return the list of information about all TV shows offered by Hulu. 
        @param limiter -- int -- The maximum number of shows to extract.
        @return [[String]]
    """
    HULU  = "https://www.hulu.com/start/more_content?&page=1&sort=alpha&video_type=tv"
    shows = list() # List of show information offered by Hulu.
    soup  = scraping.brewsoup(HULU) # BeautifulSoup4 object from HULU url.
    total = int(soup.find(class_="total").get_text()) # Total number of pages to scrape.
    for page in range(1, 1 + total):
        url   = HULU.replace("page=1", "page={}".format(page))
        soup  = scraping.brewsoup(url)
        table = soup.find("table")
        for row in table.find_all("tr"):
            for cell in row.find_all("td"):
                title = cell.find(class_="channel-results-show")
                if (title): # Some td cells are empty. Must avoid scraping them.
                    name = title.find(class_="beaconid")
                    url  = name["href"].replace("hulu.com/", "hulu.com/shows/info/")
                    shows.append(showinfo(url))
                    if limiter and limiter <= len(shows): 
                        return shows # For testing small number of extractions
        print(len(shows)) # Give some visual feedback so I know computer is not frozen.
    return shows

def showinfo(url):
    """ Return show info corresponding to the show's url.
        @param url -- String -- the url of the show.
        @return [String]
    """
    soup = scraping.brewsoup(url)
    text = soup.get_text().replace("false", "False").replace("true", "True") # So eval works.
    info = ast.literal_eval(text) # Hulu encodes show info like a python dict. Lucky!
    return [info["name"],           # Name of Show
            info["channel"],        # Genre of Show
            info["seasons_count"],  # Number of Seasons
            info["episodes_count"], # Number of Episodes
            info["description"]]    # Hulu's Description
   
print("Starting...")
scraping.makeCSV(showlist(), "hulu.csv")
print("All Done!")