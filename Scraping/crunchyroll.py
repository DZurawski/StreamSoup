""" === Scraping TV Shows from Crunchyroll ===
    @author Daniel Zurawski
"""

import scraping

def showlist(limiter=None):
    """ Return the list of information about all TV shows offered by Crunchyroll. 
        @param limiter -- int -- the maximum number of shows to extract
        @return [[String]]
    """
    CROLL = "http://www.crunchyroll.com/videos/anime/alpha?group=all"
    shows = list() # List of show information offered by Crunchyroll.
    soup  = scraping.brewsoup(CROLL) # BeautifulSoup4 object from Crunchyroll url.
    table = soup.find(class_="videos-column-container cf")
    for column in table.find_all(class_="videos-column left"):
        for cell in column.find_all("ul"):
            for show in cell.find_all("li"):
                url = "http://www.crunchyroll.com{}".format(show.find("a")["href"])
                info = showinfo(url)
                shows.append(info)
                if limiter and limiter <= len(shows): 
                    return shows # For testing small number extractions
                print(info[0].encode("utf-8")) # Visual feedback so I know computer is not frozen.
    return shows

def showinfo(url):
    """ Return show info corresponding to the show's url.
        @param url -- String -- the url of the show.
        @return [String]
    """
    soup  = scraping.brewsoup(url)
    elem  = soup.find(id="sidebar_elements").find_all("li", class_="large-margin-bottom")
    desc  = soup.find(class_="description")
    extra = dict() # Extra information.   
    extra["Name"] = soup.find("h1", class_="ellipsis").get_text().strip()   
    if desc.find(class_="more"):
        extra["Desc"] = desc.find(class_="more").get_text().strip()
    else:
        extra["Desc"] = desc.get_text().strip() 
    for li in elem[-1].find_all("li"):
        li  = li.get_text().split(":")
        key = li[0]
        val = ":".join(li[1:])
        extra[key.strip()] = " ".join(val.strip().split())    
    return [extra.get("Name"),      # Name of Show
            extra.get("Tags"),      # Tags associated with show
            extra.get("Videos"),    # Number of episodes/videos
            extra.get("Year"),      # Year of airing
            extra.get("Publisher"), # Publisher
            extra.get("Desc")]      # Description of show
    

print("Starting...")
scraping.makeCSV(showlist(), "crunchyroll.csv")
print("All Done!")